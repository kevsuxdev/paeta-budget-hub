<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\BudgetLineItem;
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
        $budget->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Budget approved successfully.');
    }

    public function rejectBudget(Budget $budget)
    {
        $budget->update(['status' => 'rejected']);
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

        $department = null;
        if ($request->department_id) {
            $dept = Department::find($request->department_id);
            $department = $dept ? $dept->name : null;
        }

        User::create([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'name' => $request->full_name, // For compatibility
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make('password'),
            'role' => $request->role,
            'department' => $department,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }
}
