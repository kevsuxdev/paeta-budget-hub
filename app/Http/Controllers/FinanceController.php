<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\BudgetLog;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function dashboard()
    {
        $pendingReview = Budget::where('status', 'pending')->count();
        $totalAmount = Budget::sum('total_budget');
        $averageAmount = Budget::avg('total_budget');

        return view('finance.dashboard', compact('pendingReview', 'totalAmount', 'averageAmount'));
    }

    public function review()
    {
        $pendingReview = Budget::where('status', 'pending')->count();
        $totalAmount = Budget::sum('total_budget');
        $averageAmount = Budget::avg('total_budget');

        $budgets = Budget::with('user', 'department')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'approved' THEN 2 ELSE 3 END")
            ->orderBy('submission_date', 'desc')
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
            $budget->update(['status' => $newStatus]);

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

    public function getBudgetLogs(Budget $budget)
    {
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