@extends('layouts.auth-layout')

@section('main-content')
<div class="p-6">
    <article class="space-y-2">
        <h1 class="text-3xl font-bold text-main">Document Tracking</h1>
        <p class="text-main font-medium mb-6">Track and manage all budget requests with search and filter capabilities.</p>
    </article>
    <!-- Alert Messages -->
    @if(session('success'))
    <x-alert-message type="success" :message="session('success')" />
    @endif
    @if(session('info'))
    <x-alert-message type="info" :message="session('info')" />
    @endif
    @if(session('error'))
    <x-alert-message type="error" :message="session('error')" />
    @endif

    <!-- Search and Filter -->
 <div class="bg-orange-brown p-4 rounded-lg shadow-sm border border-primary mb-6">
    <form method="GET" action="{{ route('admin.document.tracking') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-white mb-1">Search</label>
            <input type="text" name="search" id="search" value="" placeholder="Search by title or user name" class="w-full border border-black/20 bg-input text-black rounded-md p-2 text-sm">
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-white mb-1">Filter by Status</label>
            <select name="status" id="status" class="w-full bg-primary/50 border border-black/20 text-white rounded-md p-2 text-sm">
                <option class="text-primary" value="">All Status</option>
                <option class="text-primary" value="pending">Pending</option>
                <option class="text-primary" value="reviewed">Reviewed</option>
                <option class="text-primary" value="finance_reviewed">Finance Reviewed</option>
                <option class="text-primary" value="revise">Revise</option>
                <option class="text-primary" value="approved">Approved</option>
                <option class="text-primary" value="rejected">Rejected</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="bg-primary text-sm text-white px-4 py-2 rounded-md hover:bg-secondary">Search</button>
        </div>
    </form>
</div>

    <!-- Budget Requests Table -->
    <div class="bg-white rounded-lg shadow-sm border border-primary overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-primary">
                <thead class="bg-orange-brown">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs bg-primary/50 font-medium text-white uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs bg-primary/50 font-medium text-white uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs bg-primary/50 font-medium text-white uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs bg-primary/50 font-medium text-white uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs bg-primary/50 font-medium text-white uppercase tracking-wider">Total Budget</th>
                        <th class="px-6 py-3 text-left text-xs bg-primary/50 font-medium text-white uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs bg-primary/50 font-medium text-white uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs bg-primary/50 font-medium text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-orange-brown divide-y divide-primary">
                    @forelse($budgets as $budget)
                    <tr>
                        <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm text-white"># {{ $budget->id }}</td>
                        <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm text-white">{{ $budget->title }}</td>
                        <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm text-white">{{ $budget->user->full_name ?? 'N/A' }}</td>
                        <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm text-white">{{ $budget->department->name ?? 'N/A' }}</td>
                        <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm text-white">{{ number_format($budget->total_budget, 2) }}</td>
                        <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm text-white">
                            <x-budget.status-badge :status="$budget->status" />
                        </td>
                        <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm text-white">{{ $budget->submission_date->format('M d, Y') }}</td>
                        <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                @if(auth()->id() == $budget->user_id && ($budget->status == 'pending' || $budget->status == 'revise'))
                                <a href="{{ route('admin.budget.edit', $budget->id) }}" class="ml-2 inline-block bg-input text-black px-3 py-2 rounded-md hover:bg-input/80 text-sm">Edit</a>
                                @endif
                                @if(auth()->id() == $budget->user_id && $budget->status == 'pending')
                                <form action="{{ route('admin.budget.destroy', $budget->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                <button type="submit" class="ml-2 inline-block bg-red-700 text-white px-3 py-2 rounded-md hover:bg-red-800 text-sm" onclick="return confirm('Are you sure you want to delete this budget?');">Delete</button>
                                </form>
                                @endif
                                <x-button
                                    type="button"
                                    class="btn-view-budget hover:bg-secondary"
                                    data-budget-id="{{ $budget->id }}"
                                    data-budget-title="{{ $budget->title }}"
                                    data-budget-status="{{ $budget->status }}"
                                    data-budget-date="{{ $budget->submission_date->format('M d, Y') }}"
                                    data-budget-user="{{ $budget->user->full_name ?? 'N/A' }}">
                                    View Details
                                </x-button>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                            No budget requests found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($budgets->hasPages())
        <div class="bg-white px-4 py-3  text-white border-t border-gray-200 sm:px-6">
            {{ $budgets->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Modals -->
    <x-budget.details-modal />
    <x-budget.status-modal :userRole="auth()->user()->role" />
</div>

<!-- JavaScript -->
<script src="{{ asset('js/budget-tracking.js') }}"></script>
@endsection