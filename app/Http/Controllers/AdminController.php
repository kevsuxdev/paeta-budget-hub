<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\BudgetLineItem;
use App\Models\BudgetLog;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function changeUserPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'new_password' => 'required|string|min:6',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->password = bcrypt($request->new_password);
        $user->already_reset_password = true;
        $user->save();

        // Log the password change for notification (no budget_id, so use null)
        BudgetLog::create([
            'budget_id' => null,
            'user_id' => $user->id,
            'action' => 'password_changed',
            'old_status' => null,
            'new_status' => null,
            'notes' => 'Password was changed by admin',
        ]);

        return redirect()->back()->with('success', 'Password changed successfully for ' . $user->full_name);
    }
    public function downloadBudgetPdf(Budget $budget)
    {

        $budget->load(['user', 'department', 'lineItems']);

        $logoPath = public_path('assets/logo.png');
        $logoBase64 = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $signPath = $budget->e_signed
            ? storage_path('app/public/' . $budget->e_signed)
            : null;

        $signatureBase64 = ($signPath && file_exists($signPath))
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($signPath))
            : null;

        $pdf = Pdf::loadView('admin.budget-pdf', [
            'budget' => $budget,
            'logo' => $logoBase64,
            'esignature' => $signatureBase64,
        ]);

        $filename = 'budget_' . $budget->id . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }
    public function dashboard()
    {
        $totalBudgets = Budget::count();
        $pendingRequests = Budget::where('status', 'pending')->count();
        $approvedProjects = Budget::where('status', 'approved')->count();
        $rejectedProjects = Budget::where('status', 'rejected')->count();

        $recentBudgets = Budget::with('user', 'department')
            ->orderBy('submission_date', 'desc')
            ->limit(5)
            ->get();

        $departmentBudgets = Budget::where('status', 'approved')
            ->join('departments', 'budgets.department_id', '=', 'departments.id')
            ->selectRaw('departments.name as department_name, SUM(budgets.total_budget) as total')
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('total', 'desc')
            ->get();

        // Fetch recent budget logs for notifications (last 20)
        $notifications = BudgetLog::with(['budget', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.dashboard', compact('totalBudgets', 'pendingRequests', 'approvedProjects', 'rejectedProjects', 'recentBudgets', 'departmentBudgets', 'notifications'));
    }

    public function createBudget()
    {
        $departments = Department::all();
        return view('admin.budget.create', compact('departments'));
    }

    public function storeBudget(Request $request)
    {
        try {
            $request->validate([
                'department_id' => 'required|exists:departments,id',
                'title' => 'required|string|max:255',
                'justification' => 'nullable|string',
                'fiscal_year' => 'required|string',
                'category' => 'required|string',
                'submission_date' => 'required|date|after:today',
                'supporting_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
                'line_items' => 'required|array|min:1',
                'line_items.*.description' => 'required|string',
                'line_items.*.quantity' => 'required|integer|min:1',
                'line_items.*.unit_cost' => 'required|numeric|min:0',
            ]);

            $totalBudget = 0;
            foreach ($request->line_items as $item) {
                $totalBudget += $item['quantity'] * $item['unit_cost'];
            }

            // Ensure documents directory exists in public disk
            Storage::disk('public')->makeDirectory('documents');

            $budget = Budget::create([
                'user_id' => Auth::user()->id,
                'department_id' => $request->department_id,
                'title' => $request->title,
                'justification' => $request->justification,
                'fiscal_year' => $request->fiscal_year,
                'category' => $request->category,
                'submission_date' => $request->submission_date,
                'total_budget' => $totalBudget,
                'supporting_document' => $request->file('supporting_document')?->store('documents', 'public'),
                'status' => 'pending',
            ]);

            foreach ($request->line_items as $item) {
                BudgetLineItem::create([
                    'budget_id' => $budget->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['quantity'] * $item['unit_cost'],
                ]);
            }

            // Create initial log entry
            BudgetLog::create([
                'budget_id' => $budget->id,
                'user_id' => Auth::id(),
                'action' => 'created',
                'old_status' => null,
                'new_status' => 'pending',
                'notes' => 'Budget request created by ' . Auth::user()->full_name,
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Budget submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function documentTracking(Request $request)
    {
        $query = Budget::with('user', 'department');

        // Search by title or user name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $budgets = $query->latest()
            ->paginate(10);

        return view('admin.document-tracking', compact('budgets'));
    }

    public function financeReview()
    {
        $pendingReview = Budget::where('status', 'pending')->count();
        $totalAmount = Budget::sum('total_budget');
        $averageAmount = Budget::avg('total_budget');

        $budgets = Budget::with('user', 'department')
            ->latest()
            ->get();

        return view('admin.finance-review', compact('pendingReview', 'totalAmount', 'averageAmount', 'budgets'));
    }

    public function approveBudget(Budget $budget)
    {
        $oldStatus = $budget->status;
        $budget->update(['status' => 'approved']);

        BudgetLog::create([
            'budget_id' => $budget->id,
            'user_id' => Auth::id(),
            'action' => 'status_changed',
            'old_status' => $oldStatus,
            'new_status' => 'approved',
            'notes' => 'Budget approved by ' . Auth::user()->full_name,
        ]);

        return redirect()->back()->with('success', 'Budget approved successfully.');
    }

    public function rejectBudget(Budget $budget)
    {
        $oldStatus = $budget->status;
        $budget->update(['status' => 'rejected']);

        BudgetLog::create([
            'budget_id' => $budget->id,
            'user_id' => Auth::id(),
            'action' => 'status_changed',
            'old_status' => $oldStatus,
            'new_status' => 'rejected',
            'notes' => 'Budget rejected by ' . Auth::user()->full_name,
        ]);

        return redirect()->back()->with('success', 'Budget rejected successfully.');
    }

    public function updateBudgetStatus(Request $request, Budget $budget)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,finance_reviewed,revise,approved,rejected',
            'remarks' => 'nullable|string|max:500',
        ]);

        $oldStatus = $budget->status;
        $newStatus = $request->status;

        if ($oldStatus !== $newStatus) {
            $updateData = [
                'status' => $newStatus,
                'date_updated' => now(),
            ];
            if ($newStatus === 'reviewed') {
                $updateData['dept_head_reviewed_at'] = now();
            }
            if ($newStatus === 'finance_reviewed') {
                $updateData['finance_reviewed_at'] = now();
            }
            if ($newStatus === 'approved') {
                $updateData['final_approved_at'] = now();
            }
            $budget->update($updateData);

            $remarks = $request->remarks ?? 'Status changed from ' . ucfirst($oldStatus) . ' to ' . ucfirst($newStatus) . ' by ' . Auth::user()->full_name;

            BudgetLog::create([
                'budget_id' => $budget->id,
                'user_id' => Auth::id(),
                'action' => 'status_changed',
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'notes' => $remarks,
            ]);

            return redirect()->back()->with('success', 'Budget status updated successfully.');
        }

        return redirect()->back()->with('info', 'No changes made to budget status.');
    }

    public function approval()
    {
        $pendingApproval = Budget::where('status', 'finance_reviewed')->count();
        $totalAmount = Budget::where('status', 'finance_reviewed')->sum('total_budget');
        $averageAmount = Budget::where('status', 'finance_reviewed')->avg('total_budget') ?? 0;

        $budgets = Budget::with('user', 'department')
            ->where('status', 'finance_reviewed')
            ->orderBy('submission_date', 'desc')
            ->paginate(10);

        return view('admin.approval', compact('pendingApproval', 'totalAmount', 'averageAmount', 'budgets'));
    }

    public function finalApproveBudget(Budget $budget)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $rules = [
            'approver_name' => 'required|string|max:255',
            'acknowledge' => 'required|accepted',
        ];
        // Only require e-signature upload if user has no e_signed
        if (empty($user->e_signed)) {
            $rules['e_signature'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        }
        $validated = request()->validate($rules);

        $filePath = null;

        if (empty($user->e_signed) && request()->hasFile('e_signature')) {
            $file = request()->file('e_signature');
            $fileName = 'esign_user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('e-signatures', $fileName, 'public');
            // Save to user profile
            $user->e_signed = $filePath;
            $user->save();
        }

        $oldStatus = $budget->status;
        $budget->update([
            'status' => 'approved',
            // Always store the path in the budget, from user profile
            'e_signed' => $user->e_signed,
            'approved_by' => $validated['approver_name'],
        ]);

        // If the department has savings (budget_release), deduct from it up to the budget amount
        $department = Department::find($budget->department_id);
        if ($department && $department->budget_release > 0) {
            $available = (float) $department->budget_release;
            $needed = (float) $budget->total_budget;
            $deduct = min($available, $needed);

            $oldRelease = $department->budget_release;
            $department->budget_release = max(0, $oldRelease - $deduct);
            $department->save();

            BudgetLog::create([
                'budget_id' => $budget->id,
                'user_id' => $user->id,
                'action' => 'savings_used',
                'old_status' => null,
                'new_status' => null,
                'notes' => sprintf('Used ₱%s from %s savings toward budget #%d — savings decreased from ₱%s to ₱%s', number_format($deduct, 2), $department->name, $budget->id, number_format($oldRelease, 2), number_format($department->budget_release, 2)),
            ]);
        }

        BudgetLog::create([
            'budget_id' => $budget->id,
            'user_id' => $user->id,
            'action' => 'final_approval',
            'old_status' => $oldStatus,
            'new_status' => 'approved',
            'notes' => 'Budget finally approved by ' . $validated['approver_name'],
        ]);

        return redirect()->back()->with('success', 'Budget approved successfully.');
    }

    public function finalRejectBudget(Budget $budget)
    {
        $oldStatus = $budget->status;
        $budget->update(['status' => 'rejected']);

        BudgetLog::create([
            'budget_id' => $budget->id,
            'user_id' => Auth::id(),
            'action' => 'final_rejection',
            'old_status' => $oldStatus,
            'new_status' => 'rejected',
            'notes' => 'Budget finally rejected by ' . Auth::user()->full_name,
        ]);

        return redirect()->back()->with('success', 'Budget rejected successfully.');
    }

    public function userManagement()
    {
        $totalUsers = User::count();
        $totalActiveUsers = User::where('status', 'active')->count();
        $totalDepartments = Department::count();
        $departments = Department::all();
        $users = User::all();

        return view('admin.user-management', compact('totalUsers', 'totalActiveUsers', 'totalDepartments', 'departments', 'users'));
    }

    public function getBudgetLogs(Budget $budget)
    {
        $budget->load(['logs.user.department']);

        $logs = $budget->logs->map(function ($log) {
            return [
                'id' => $log->id,
                'action' => $log->action,
                'old_status' => $log->old_status,
                'new_status' => $log->new_status,
                'notes' => $log->notes,
                'user_name' => $log->user ? $log->user->full_name : 'System',
                'department_name' => $log->user && $log->user->department ? $log->user->department->name : 'N/A',
                'timestamp' => $log->created_at->format('M d, Y h:i A'),
            ];
        });

        $budget->load('lineItems');
        return response()->json([
            'logs' => $logs,
            'budget' => [
                'id' => $budget->id,
                'title' => $budget->title,
                'fiscal_year' => $budget->fiscal_year,
                'category' => $budget->category,
                'total_budget' => $budget->total_budget,
                'justification' => $budget->justification,
                'status' => $budget->status,
                'supporting_document' => $budget->supporting_document,
                'e_signed' => $budget->e_signed,
                'line_items' => $budget->lineItems->map(function ($item) {
                    return [
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_cost' => $item->unit_cost,
                        'total_cost' => $item->total_cost,
                    ];
                }),
            ]
        ]);
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
        ]);

        Department::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Department added successfully.');
    }

    public function destroyDepartment(Department $department)
    {
        $department->delete();
        return redirect()->back()->with('success', 'Department deleted successfully.');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:255',
            'role' => 'required|in:admin,finance,dept_head,staff',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'required|in:active,inactive',
        ]);

        User::create([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make('paete@password'),
            'role' => $request->role,
            'department_id' => $request->department_id,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255',
            'role' => 'required|in:admin,finance,dept_head,staff',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'required|in:active,inactive',
        ]);

        $user->update([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'department_id' => $request->department_id,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        // Prevent deleting self
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function auditTrail(Request $request)
    {
        // Statistics
        $totalApproved = Budget::where('status', 'approved')->count();
        $totalActivities = BudgetLog::count();
        $totalBudgetsSubmitted = Budget::count();
        $activeUsers = User::where('status', 'active')->count();

        // Get activity logs with search functionality
        $search = $request->input('search');

        $logs = BudgetLog::with(['budget', 'user'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('budget', function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.audit-trail', compact(
            'totalApproved',
            'totalActivities',
            'totalBudgetsSubmitted',
            'activeUsers',
            'logs',
            'search'
        ));
    }

    public function archive(Request $request)
    {
        // Statistics
        $totalArchived = Budget::whereIn('status', ['approved', 'rejected'])->count();
        $approvedBudgets = Budget::where('status', 'approved')->count();

        // Calculate total value with formatting
        $totalValue = Budget::whereIn('status', ['approved'])->sum('total_budget');
        $formattedTotalValue = $this->formatCurrency($totalValue);

        $totalDepartments = Department::count();

        // Get search and filter inputs
        $search = $request->input('search');
        $statusFilter = $request->input('status');

        // Get archived budgets with search and filter functionality
        $budgets = Budget::with(['user', 'department'])
            ->whereIn('status', ['approved', 'rejected'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', '%' . $search . '%')
                        ->orWhere('title', 'like', '%' . $search . '%')
                        ->orWhereHas('department', function ($dept) use ($search) {
                            $dept->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($statusFilter, function ($query, $statusFilter) {
                return $query->where('status', $statusFilter);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('admin.archive', compact(
            'totalArchived',
            'approvedBudgets',
            'formattedTotalValue',
            'totalValue',
            'totalDepartments',
            'budgets',
            'search',
            'statusFilter'
        ));
    }

    private function formatCurrency($amount)
    {
        if ($amount >= 1000000) {
            return '₱' . number_format($amount / 1000000, 2) . 'M';
        } elseif ($amount >= 1000) {
            return '₱' . number_format($amount / 1000, 2) . 'k';
        }
        return '₱' . number_format($amount, 2);
    }
}
