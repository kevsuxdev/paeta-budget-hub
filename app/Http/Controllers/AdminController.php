<?php

namespace App\Http\Controllers;

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

        return view('admin.dashboard', compact('totalBudgets', 'pendingRequests', 'approvedProjects', 'rejectedProjects', 'recentBudgets'));
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
                'submission_date' => 'required|date',
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

            // Ensure documents directory exists
            Storage::makeDirectory('documents');

            $budget = Budget::create([
                'user_id' => Auth::user()->id,
                'department_id' => $request->department_id,
                'title' => $request->title,
                'justification' => $request->justification,
                'fiscal_year' => $request->fiscal_year,
                'category' => $request->category,
                'submission_date' => $request->submission_date,
                'total_budget' => $totalBudget,
                'supporting_document' => $request->file('supporting_document')?->store('documents'),
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
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $budgets = $query->orderBy('submission_date', 'desc')->paginate(10);

        return view('admin.document-tracking', compact('budgets'));
    }

    public function financeReview()
    {
        $pendingReview = Budget::where('status', 'pending')->count();
        $totalAmount = Budget::sum('total_budget');
        $averageAmount = Budget::avg('total_budget');

        $budgets = Budget::with('user', 'department')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'approved' THEN 2 ELSE 3 END")
            ->orderBy('submission_date', 'desc')
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
            $budget->update(['status' => $newStatus]);

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
        $oldStatus = $budget->status;
        $budget->update(['status' => 'approved']);

        BudgetLog::create([
            'budget_id' => $budget->id,
            'user_id' => Auth::id(),
            'action' => 'final_approval',
            'old_status' => $oldStatus,
            'new_status' => 'approved',
            'notes' => 'Budget finally approved by ' . Auth::user()->full_name,
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

        $logs = $budget->logs->map(function($log) {
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

        return response()->json([
            'logs' => $logs,
            'budget' => [
                'id' => $budget->id,
                'title' => $budget->title,
                'fiscal_year' => $budget->fiscal_year,
                'category' => $budget->category,
                'total_budget' => number_format($budget->total_budget, 2),
                'justification' => $budget->justification,
                'status' => $budget->status,
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
            'password' => Hash::make('password'),
            'role' => $request->role,
            'department_id' => $request->department_id,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }
}
