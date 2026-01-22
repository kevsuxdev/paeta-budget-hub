<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\BudgetLineItem;
use App\Models\BudgetLog;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Department statistics
        $deptBudgets = Budget::where('user_id', $user->id);

        $totalDeptBudgets = $deptBudgets->count();
        $pendingDeptBudgets = (clone $deptBudgets)->where('status', 'pending')->count();
        $approvedDeptBudgets = (clone $deptBudgets)->where('status', 'approved')->count();

        return view('staff.dashboard', compact(
            'user',
            'totalDeptBudgets',
            'pendingDeptBudgets',
            'approvedDeptBudgets'
        ));
    }

    public function createBudget()
    {
        $user = Auth::user();
        $departments = Department::all();

        return view('staff.budget.create', compact('departments', 'user'));
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

            Storage::makeDirectory('documents');

            $budget = Budget::create([
                'user_id' => Auth::id(),
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

            return redirect()->route('staff.dashboard')->with('success', 'Budget submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function documentTracking(Request $request)
    {
        $user = Auth::user();
        $query = Budget::with('user', 'department')
            ->where('user_id', $user->id);

        // Search by title or user name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $budgets = $query->orderBy('submission_date', 'desc')->paginate(10);

        return view('staff.document-tracking', compact('budgets'));
    }

    public function getBudgetLogs(Budget $budget)
    {
        // Verify the budget belongs to the current user
        if ($budget->user_id !== Auth::id()) {
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
