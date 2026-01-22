@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-primary mb-4">Final Approval</h1>
    <p class="text-gray-600 mb-6">Review and give final approval to budgets that have passed finance review.</p>

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

    <!-- Overview Statistics -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Approval Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Pending Approval -->
            <div class="flex items-center">
                <svg class="w-10 h-10 text-purple-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Pending Approval</p>
                    <p class="text-2xl font-bold text-purple-500">{{ $pendingApproval }}</p>
                </div>
            </div>

            <!-- Total Amount -->
            <div class="flex items-center">
                <svg class="w-10 h-10 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Total Amount</p>
                    <p class="text-2xl font-bold text-green-500">₱{{ number_format($totalAmount, 2) }}</p>
                </div>
            </div>

            <!-- Average Amount -->
            <div class="flex items-center">
                <svg class="w-10 h-10 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Average Amount</p>
                    <p class="text-2xl font-bold text-blue-500">₱{{ number_format($averageAmount, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Requests List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Budgets for Final Approval</h3>
            <p class="text-sm text-gray-500">
                Showing <span class="font-medium">{{ $budgets->firstItem() ?? 0 }}</span> to
                <span class="font-medium">{{ $budgets->lastItem() ?? 0 }}</span> of
                <span class="font-medium">{{ $budgets->total() }}</span> results
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($budgets as $budget)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $budget->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $budget->title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $budget->user->full_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $budget->department->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($budget->total_budget, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-budget.status-badge :status="$budget->status" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($budget->submission_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <button
                                    class="btn-view-budget text-blue-600 hover:text-blue-900 font-medium"
                                    data-budget-id="{{ $budget->id }}"
                                    data-budget-title="{{ $budget->title }}"
                                    data-budget-status="{{ $budget->status }}"
                                    data-budget-date="{{ \Carbon\Carbon::parse($budget->submission_date)->format('M d, Y') }}"
                                    data-budget-user="{{ $budget->user->full_name }}"
                                >
                                    View Details
                                </button>
                                <span class="text-gray-300">|</span>
                                <form action="{{ route('admin.budget.finalApprove', $budget->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="text-green-600 hover:text-green-900 font-medium"
                                        onclick="return confirm('Are you sure you want to approve this budget?')"
                                    >
                                        Approve
                                    </button>
                                </form>
                                <span class="text-gray-300">|</span>
                                <form action="{{ route('admin.budget.finalReject', $budget->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="text-red-600 hover:text-red-900 font-medium"
                                        onclick="return confirm('Are you sure you want to reject this budget?')"
                                    >
                                        Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900 mb-1">No budgets pending final approval</p>
                                    <p class="text-gray-500">All finance-reviewed budgets have been processed.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($budgets->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $budgets->links() }}
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
