<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\BudgetLineItem;
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
        $userIds = User::where('department_id', $user->department->id)->pluck('id');
        $deptBudgets = Budget::whereIn('user_id', $userIds);

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
                'user_id' => Auth::user()->id,
                'department_id' => Auth::user()->department ? Department::where('name', Auth::user()->department)->first()->id : null,
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
}
