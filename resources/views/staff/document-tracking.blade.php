@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-white">Document Tracking</h1>
    <p class="text-white mb-6">Track budget requests from your department with search and filter capabilities.</p>

    <!-- Search and Filter -->
    <div class="bg-orange-brown p-4 rounded-lg mb-6">
        <form method="GET" action="{{ route('staff.document.tracking') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-white mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by title or user name" class="w-full border border-black/20 text-white rounded-md p-2 text-sm">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-white mb-1">Filter by Status</label>
                <select name="status" id="status" class="w-full border border-black/20 rounded-md p-2 text-white text-sm">
                    <option class="text-primary" value="">All Status</option>
                    <option class="text-primary" value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option class="text-primary" value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                    <option class="text-primary" value="finance_reviewed" {{ request('status') == 'finance_reviewed' ? 'selected' : '' }}>Finance Reviewed</option>
                    <option class="text-primary" value="revise" {{ request('status') == 'revise' ? 'selected' : '' }}>Revise</option>
                    <option class="text-primary" value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option class="text-primary" value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-primary text-white px-4 py-2 text-sm rounded-md hover:bg-opacity-90">Search</button>
            </div>
        </form>
    </div>

    <!-- Budget Requests Table -->
    <div class="bg-orange-brown rounded-lg shadow-sm border border-primary overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-primary">
                <thead class="bg-orange-brown">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Total Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-orange-brown divide-y divide-primary">
                    @forelse($budgets as $budget)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $budget->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $budget->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $budget->user->full_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $budget->department->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">₱{{ number_format($budget->total_budget, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-budget.status-badge :status="$budget->status" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $budget->submission_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button
                                class="btn-view-budget bg-primary text-white px-4 py-2 rounded-md cursor-pointer font-medium"
                                data-budget-id="{{ $budget->id }}"
                                data-budget-title="{{ $budget->title }}"
                                data-budget-status="{{ $budget->status }}"
                                data-budget-date="{{ \Carbon\Carbon::parse($budget->submission_date)->format('M d, Y') }}"
                                data-budget-user="{{ $budget->user->full_name }}">
                                View Details
                            </button>
                            @if(auth()->id() == $budget->user_id && ($budget->status == 'pending' || $budget->status == 'revise'))
                            <a href="{{ route('staff.budget.edit', $budget->id) }}" class="ml-2 inline-block bg-white/10 text-white px-3 py-2 rounded-md hover:bg-opacity-90 text-sm">Edit</a>
                            @endif
                            @if(auth()->id() == $budget->user_id && $budget->status == 'pending')
                            <form action="{{ route('staff.budget.destroy', $budget->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-2 inline-block bg-red-600 text-white px-3 py-2 rounded-md hover:bg-red-700 text-sm" onclick="return confirm('Are you sure you want to delete this budget?');">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-white">No budget requests found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($budgets->hasPages())
        <div class="bg-orange-brown px-4 py-3 border-t border-primary sm:px-6">
            {{ $budgets->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Budget Details Modal -->
<x-budget.details-modal />

<!-- Include Budget Tracking JS -->
<script src="{{ asset('js/budget-tracking.js') }}"></script>
<script>
    $(document).ready(function() {
        BudgetTracking.init();
    });
</script>
@endsection