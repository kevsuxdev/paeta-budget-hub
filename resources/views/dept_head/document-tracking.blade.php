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
        :statusValue="request('status', '')"
    />

    <!-- Budget Requests Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
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
                        <x-budget.table-row
                            :budget="$budget"
                            :canUpdateStatus="true"
                        />
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
