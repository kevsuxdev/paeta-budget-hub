@extends('layouts.auth-layout')

@section('main-content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-primary mb-4">Document Tracking</h1>
    <p class="text-gray-600 mb-6">Track and manage budget requests from your department.</p>

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
    <x-budget.search-filter
        :route="route('dept_head.document.tracking')"
        :searchValue="request('search', '')"
        :statusValue="request('status', '')" />

    <!-- Budget Requests Table -->
    <div class="bg-orange-brown rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
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
                                <x-button
                                    type="button"
                                    class="btn-view-budget"
                                    data-budget-id="{{ $budget->id }}"
                                    data-budget-title="{{ $budget->title }}"
                                    data-budget-status="{{ $budget->status }}"
                                    data-budget-date="{{ $budget->submission_date->format('M d, Y') }}"
                                    data-budget-user="{{ $budget->user->full_name ?? 'N/A' }}">
                                    View Details
                                </x-button>

                                @if(auth()->id() == $budget->user_id && ($budget->status == 'pending' || $budget->status == 'revise'))
                                    <a href="{{ route('staff.budget.edit', $budget->id) }}" class="ml-2 inline-block bg-white/10 text-white px-3 py-2 rounded-md hover:bg-opacity-90 text-sm">Edit</a>
                                @endif

                                <button
                                    @disabled($budget->status !== 'pending')
                                    type="button"
                                    class="btn-update-status inline-flex items-center px-3 py-2 border disabled:bg-gray-400 disabled:border-none disabled:cursor-not-allowed border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                                    data-budget-id="{{ $budget->id }}"
                                    data-budget-status="{{ $budget->status }}">
                                    Update Status
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                            No budget requests found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($budgets->hasPages())
    <div class="mt-6">
        {{ $budgets->links() }}
    </div>
    @endif
</div>

<!-- Budget Details Modal -->
<x-budget.details-modal />

<!-- Status Update Modal -->
<x-budget.status-modal userRole="dept_head" />

<!-- JavaScript -->
<script src="{{ asset('js/budget-tracking.js') }}"></script>

@endsection