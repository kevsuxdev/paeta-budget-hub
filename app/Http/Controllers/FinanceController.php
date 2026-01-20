<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;

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
        $budget->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Budget approved successfully.');
    }

    public function rejectBudget(Budget $budget)
    {
        $budget->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Budget rejected successfully.');
    }
}