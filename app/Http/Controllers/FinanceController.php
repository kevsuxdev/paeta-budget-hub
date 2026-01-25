<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\BudgetLog;
use App\Models\Department;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function dashboard()
    {
        $pendingReview = Budget::where('status', 'pending')->count();
        $totalRequests = Budget::count();
        $approvedCount = Budget::where('status', 'approved')->count();
        $rejectedCount = Budget::where('status', 'rejected')->count();
        $totalAmount = Budget::sum('total_budget');
        $averageAmount = Budget::avg('total_budget');

        // Get approved budgets grouped by department
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

        return view('finance.dashboard', compact(
            'pendingReview',
            'totalRequests',
            'approvedCount',
            'rejectedCount',
            'totalAmount',
            'averageAmount',
            'departmentBudgets',
            'notifications'
        ));
    }

    public function review()
    {
        $pendingReview = Budget::where('status', 'pending')->count();
        $totalAmount = Budget::sum('total_budget');
        $averageAmount = Budget::avg('total_budget');

        $budgets = Budget::with('user', 'department')
            ->latest()
            ->get();

        return view('finance.review', compact('pendingReview', 'totalAmount', 'averageAmount', 'budgets'));
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
    private function formatCurrency($amount)
    {
        if ($amount >= 1000000) {
            return '₱' . number_format($amount / 1000000, 2) . 'M';
        } elseif ($amount >= 1000) {
            return '₱' . number_format($amount / 1000, 2) . 'k';
        }
        return '₱' . number_format($amount, 2);
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

        return view('finance.archive', compact(
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

        return view('finance.audit-trail', compact(
            'totalApproved',
            'totalActivities',
            'totalBudgetsSubmitted',
            'activeUsers',
            'logs',
            'search'
        ));
    }
    public function updateBudgetStatus(Request $request, Budget $budget)
    {
        $request->validate([
            'status' => 'required|in:finance_reviewed,revise,approved,rejected',
            'remarks' => 'nullable|string|max:500',
        ]);

        $oldStatus = $budget->status;
        $newStatus = $request->status;

        // When finance selects "approved", change it to "finance_reviewed"
        if ($newStatus === 'approved') {
            $newStatus = 'finance_reviewed';
        }

        if ($oldStatus !== $newStatus) {
            $updateData = [
                'status' => $newStatus,
                'date_updated' => now(),
            ];
            if ($newStatus === 'finance_reviewed') {
                $updateData['finance_reviewed_at'] = now();
            }
            $budget->update($updateData);

            $remarks = $request->remarks ?? 'Status changed from ' . ucfirst($oldStatus) . ' to ' . ucfirst(str_replace('_', ' ', $newStatus)) . ' by ' . Auth::user()->full_name;

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

        return view('finance.approval', compact('pendingApproval', 'totalAmount', 'averageAmount', 'budgets'));
    }

    public function finalApproveBudget(Budget $budget)
    {
        // Validate the request
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
            'date_updated' => now(),
            'final_approved_at' => now(),
        ]);

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
        $budget->update([
            'status' => 'rejected',
            'date_updated' => now(),
        ]);

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

    public function getBudgetLogs(Budget $budget)
    {
        // Eager load lineItems for the budget
        $budget->load('lineItems');

        $logs = BudgetLog::where('budget_id', $budget->id)
            ->with(['user.department'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'action' => $log->action,
                    'old_status' => $log->old_status,
                    'new_status' => $log->new_status,
                    'notes' => $log->notes,
                    'user_name' => $log->user->full_name ?? 'Unknown User',
                    'department_name' => $log->user->department->name ?? 'Unknown Department',
                    'timestamp' => $log->created_at->format('M d, Y h:i A'),
                ];
            });

        return response()->json([
            'budget' => $budget,
            'logs' => $logs,
        ]);
    }
}
