@extends('layouts.auth-layout')

@section('main-content')
<div class="p-6">
    <article class="space-y-2">
        <h1 class="text-2xl font-bold text-white">Document Tracking</h1>
        <p class="text-primary mb-6">Track and manage all budget requests with search and filter capabilities.</p>
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
    <x-budget.search-filter
        :route="route('admin.document.tracking')"
        :searchValue="request('search', '')"
        :statusValue="request('status', '')"
    />

    <!-- Budget Requests Table -->
    <div class="bg-white rounded-lg shadow-sm border border-primary overflow-hidden">
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
                        <x-budget.table-row
                            :budget="$budget"
                            :canUpdateStatus="auth()->user()->role === 'admin' || auth()->user()->role === 'dept_head'"
                        />
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