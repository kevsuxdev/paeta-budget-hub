@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <article class="space-y-2">
        <h1 class="text-2xl font-bold text-white">Finance Review</h1>
        <p class="text-white mb-6">Review and manage budget requests for financial approval.</p>
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

    <!-- Overview Statistics -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-white mb-4">Finance Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Pending Review -->
            <div class="flex items-center bg-orange-brown p-6 rounded-lg">
                <div>
                    <p class="text-sm text-white">Pending Review</p>
                    <p class="text-2xl font-bold text-yellow-500">{{ $pendingReview }}</p>
                </div>
            </div>

            <!-- Total Amount -->
            <div class="flex items-center bg-orange-brown p-6 rounded-lg">
                <div>
                    <p class="text-sm text-white">Total Amount</p>
                    <p class="text-2xl font-bold text-green-500">₱ {{ number_format($totalAmount, 2) }}</p>
                </div>
            </div>

            <!-- Average Amount -->
            <div class="flex items-center bg-orange-brown p-6 rounded-lg">
                <div>
                    <p class="text-sm text-white">Average Amount</p>
                    <p class="text-2xl font-bold text-blue-500">{{ number_format($averageAmount, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Requests List -->
    <div class="bg-orange-brown rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-primary">
            <h3 class="text-lg font-semibold text-white">Budget Requests</h3>
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
                <tbody class="bg-white divide-y divide-primary">
                    @forelse($budgets as $budget)
                        <x-budget.table-row
                            :budget="$budget"
                            :canUpdateStatus="true"
                        />
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-white">No budget requests found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Budget Details Modal -->
<x-budget.details-modal />

<!-- Status Update Modal -->
<x-budget.status-modal userRole="finance" />

<!-- JavaScript -->
<script src="{{ asset('js/budget-tracking.js') }}"></script>
@endsection