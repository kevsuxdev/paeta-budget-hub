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

        // User's budget statistics
        $totalBudgets = Budget::where('user_id', $user->id)->count();
        $pendingRequests = Budget::where('user_id', $user->id)->where('status', 'pending')->count();
        $approvedRequests = Budget::where('user_id', $user->id)->where('status', 'approved')->count();
        $rejectedRequests = Budget::where('user_id', $user->id)->where('status', 'rejected')->count();

        // Department statistics
        $deptBudgets = Budget::where('user_id', $user->id);

        $totalDeptBudgets = $deptBudgets->count();
        $pendingDeptBudgets = (clone $deptBudgets)->where('status', 'pending')->count();
        $approvedDeptBudgets = (clone $deptBudgets)->where('status', 'approved')->count();

        // Recent budget requests
        $recentRequests = Budget::where('user_id', $user->id)
            ->with('department')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('staff.dashboard', compact(
            'user',
            'totalBudgets',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'totalDeptBudgets',
            'pendingDeptBudgets',
            'approvedDeptBudgets',
            'recentRequests'
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

            return redirect()->route('staff.document.tracking')->with('success', 'Budget submitted successfully.');
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
                        $userQuery->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $budgets = $query->latest()->paginate(10);

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

        $budget->load('lineItems');
        return response()->json([
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
                'line_items' => $budget->lineItems->map(function($item) {
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
}
