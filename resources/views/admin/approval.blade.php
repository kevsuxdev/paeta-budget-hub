@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-white">Final Approval</h1>
    <p class="text-white mb-6">Review and give final approval to budgets that have passed finance review.</p>

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
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-white mb-4">Approval Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Pending Approval -->
            <div class="flex items-center bg-orange-brown rounded-lg p-6">
                <div class="ml-3">
                    <p class="text-sm text-white">Pending Approval</p>
                    <p class="text-2xl font-bold text-purple-500">{{ $pendingApproval }}</p>
                </div>
            </div>

            <!-- Total Amount -->
            <div class="flex items-center bg-orange-brown rounded-lg p-6">
                <div class="ml-3">
                    <p class="text-sm text-white">Total Amount</p>
                    <p class="text-2xl font-bold text-green-500">₱{{ number_format($totalAmount, 2) }}</p>
                </div>
            </div>

            <!-- Average Amount -->
            <div class="flex items-center bg-orange-brown rounded-lg p-6">
                <div class="ml-3">
                    <p class="text-sm text-white">Average Amount</p>
                    <p class="text-2xl font-bold text-blue-500">₱{{ number_format($averageAmount, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Requests List -->
    <div class="bg-orange-brown rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-white">Budgets for Final Approval</h3>
            <p class="text-sm text-white">
                Showing <span class="font-medium">{{ $budgets->firstItem() ?? 0 }}</span> to
                <span class="font-medium">{{ $budgets->lastItem() ?? 0 }}</span> of
                <span class="font-medium">{{ $budgets->total() }}</span> results
            </p>
        </div>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Submission Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-orange-brown divide-y divide-primary">
                    @forelse($budgets as $budget)
                        <tr class="hover:bg-primary/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $budget->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-white">{{ $budget->title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-white">{{ $budget->user->full_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-white">{{ $budget->department->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">₱ {{ number_format($budget->total_budget, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-budget.status-badge :status="$budget->status" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
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
                                    <p class="text-lg font-medium text-white mb-1">No budgets pending final approval</p>
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
