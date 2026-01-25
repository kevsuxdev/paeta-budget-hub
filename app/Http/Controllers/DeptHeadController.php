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

        // Department budget is stored on the departments table (budget_release)
        $department = Department::find($departmentId);
        $departmentTotal = $department?->budget_release ?? 0;

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

        // Fetch all budget logs for this department's budgets
        $deptBudgetIds = Budget::where('department_id', $departmentId)->pluck('id');
        $notifications = BudgetLog::with(['budget', 'user'])
            ->whereIn('budget_id', $deptBudgetIds)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('dept_head.dashboard', compact(
            'user',
            'totalBudgets',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'departmentTotal',
            'recentRequests',
            'availableYears',
            'selectedYear',
            'chartData',
            'notifications'
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

            // Ensure documents directory exists
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
                'date_updated' => now(),
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

    public function editBudget(Budget $budget)
    {
        // Verify that the budget belongs to the dept_head's department
        if ($budget->department_id !== Auth::user()->department_id) {
            return redirect()->back()->with('error', 'You can only edit budgets from your department.');
        }

        // Only allow editing when pending or revise
        if (!in_array($budget->status, ['pending', 'revise'])) {
            return redirect()->back()->with('error', 'Only pending or revised budgets can be edited.');
        }

        $departments = Department::all();
        $budget->load('lineItems');
        return view('dept_head.budget.edit', compact('departments', 'budget'));
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
            $updateData = [
                'status' => $newStatus,
                'date_updated' => now(),
            ];
            if ($newStatus === 'reviewed') {
                $updateData['dept_head_reviewed_at'] = now();
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

        // New method: fetch budget details and line items for modal
        public function getBudgetDetails(Budget $budget)
        {
            // Verify that the budget belongs to the dept_head's department
            if ($budget->department_id !== Auth::user()->department_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $budget->load(['user', 'department', 'lineItems']);

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
                'budget' => [
                    'id' => $budget->id,
                    'title' => $budget->title,
                    'user' => $budget->user->full_name ?? 'N/A',
                    'department' => $budget->department->name ?? 'N/A',
                    'total_budget' => $budget->total_budget,
                    'status' => $budget->status,
                    'submission_date' => $budget->submission_date->format('M d, Y'),
                    'justification' => $budget->justification,
                    'category' => $budget->category,
                    'fiscal_year' => $budget->fiscal_year,
                    'supporting_document' => $budget->supporting_document,
                    'e_signed' => $budget->e_signed ?? false,
                    'line_items' => $budget->lineItems->map(function ($item) {
                        return [
                            'description' => $item->description,
                            'quantity' => $item->quantity,
                            'unit_cost' => $item->unit_cost,
                            'total_cost' => $item->total_cost,
                        ];
                    }),
                ],
                'logs' => $logs,
            ]);
        }

        public function updateBudget(Request $request, Budget $budget)
        {
            // Verify owner department
            if ($budget->department_id !== Auth::user()->department_id) {
                return redirect()->back()->with('error', 'You can only edit budgets from your department.');
            }

            // Only allow editing when pending or revise
            if (!in_array($budget->status, ['pending', 'revise'])) {
                return redirect()->back()->with('error', 'Only pending or revised budgets can be edited.');
            }

            $request->validate([
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

            try {
                // calculate total
                $totalBudget = 0;
                foreach ($request->line_items as $item) {
                    $totalBudget += $item['quantity'] * $item['unit_cost'];
                }

                // handle supporting doc
                if ($request->hasFile('supporting_document')) {
                    Storage::disk('public')->makeDirectory('documents');
                    $path = $request->file('supporting_document')->store('documents', 'public');
                    // optionally delete old file
                    if ($budget->supporting_document) {
                        Storage::disk('public')->delete($budget->supporting_document);
                    }
                    $budget->supporting_document = $path;
                }

                $budget->title = $request->title;
                $budget->justification = $request->justification;
                $budget->fiscal_year = $request->fiscal_year;
                $budget->category = $request->category;
                $budget->submission_date = $request->submission_date;
                $budget->total_budget = $totalBudget;
                $budget->date_updated = now();
                $budget->save();

                // replace line items
                BudgetLineItem::where('budget_id', $budget->id)->delete();
                foreach ($request->line_items as $item) {
                    BudgetLineItem::create([
                        'budget_id' => $budget->id,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit_cost' => $item['unit_cost'],
                        'total_cost' => $item['quantity'] * $item['unit_cost'],
                    ]);
                }

                BudgetLog::create([
                    'budget_id' => $budget->id,
                    'user_id' => Auth::id(),
                    'action' => 'edited',
                    'old_status' => $budget->status,
                    'new_status' => $budget->status,
                    'notes' => 'Budget edited by ' . Auth::user()->full_name,
                ]);

                return redirect()->back()->with('success', 'Budget updated successfully.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
        }
}
