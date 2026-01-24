<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\BudgetLineItem;
use App\Models\BudgetLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeptHeadController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        // Department budget statistics
        $totalBudgets = Budget::where('department_id', $departmentId)->count();
        $pendingRequests = Budget::where('department_id', $departmentId)->where('status', 'pending')->count();
        $approvedRequests = Budget::where('department_id', $departmentId)->where('status', 'approved')->count();
        $rejectedRequests = Budget::where('department_id', $departmentId)->where('status', 'rejected')->count();

        // Recent budget requests from the department
        $recentRequests = Budget::where('department_id', $departmentId)
            ->with(['user', 'department'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get available years from approved budgets
        $availableYears = Budget::where('department_id', $departmentId)
            ->where('status', 'approved')
            ->selectRaw('DISTINCT YEAR(created_at) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Default to current year if no year is selected
        $selectedYear = $request->input('year', date('Y'));

        // Get approved budgets grouped by month for the selected year
        $monthlyData = Budget::where('department_id', $departmentId)
            ->where('status', 'approved')
            ->whereYear('created_at', $selectedYear)
            ->selectRaw('MONTH(created_at) as month, SUM(total_budget) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        // Prepare data for all 12 months
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyData->get($i, 0);
        }

        return view('dept_head.dashboard', compact(
            'user',
            'totalBudgets',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'recentRequests',
            'availableYears',
            'selectedYear',
            'chartData'
        ));
    }

    public function documentTracking(Request $request)
    {
        $userDepartmentId = Auth::user()->department_id;

        // Query budgets only from the department head's department
        $query = Budget::with('user', 'department')
            ->where('department_id', $userDepartmentId);

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

        $budgets = $query->latest()->paginate(10);

        return view('dept_head.document-tracking', compact('budgets'));
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

            return redirect()->route(route: 'dept_head.dashboard')->with('success', 'Budget submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function createBudget()
    {
        $departments = Department::all();
        return view('dept_head.budget.create', compact('departments'));
    }

    public function updateBudgetStatus(Request $request, Budget $budget)
    {
        // Verify that the budget belongs to the dept_head's department
        if ($budget->department_id !== Auth::user()->department_id) {
            return redirect()->back()->with('error', 'You can only update budgets from your department.');
        }

        $request->validate([
            'status' => 'required|in:reviewed,rejected',
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

    public function getBudgetLogs(Budget $budget)
    {
        // Verify that the budget belongs to the dept_head's department
        if ($budget->department_id !== Auth::user()->department_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
