<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\BudgetLog;
use Illuminate\Support\Facades\Auth;

class DeptHeadController extends Controller
{
    public function dashboard()
    {
        return view('dept_head.dashboard');
    }

    public function documentTracking(Request $request)
    {
        // Get the logged-in department head's department
        $userDepartmentId = Auth::user()->department_id;

        // Query budgets only from the department head's department
        $query = Budget::with('user', 'department')
            ->where('department_id', $userDepartmentId);

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

        return view('dept_head.document-tracking', compact('budgets'));
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