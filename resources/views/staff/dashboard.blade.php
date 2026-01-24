@extends('layouts.auth-layout')
@section('main-content')
@php $user = request()->user(); @endphp
@if(!$user->already_reset_password)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 flex items-center justify-between">
        <span class="font-semibold">Security Notice:</span>
        <span class="ml-2">You must reset your password before continuing to use the system.</span>
        <button onclick="openResetPasswordModal()" class="ml-4 px-3 py-1 bg-orange-600 text-white rounded hover:bg-orange-700 transition">Reset Now</button>
    </div>
    @include('components.modals.reset-password-modal')
    <script>document.addEventListener('DOMContentLoaded', openResetPasswordModal);</script>
@endif
<div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold text-white mb-4">Dashboard</h1>
    <article class="space-y-2">
        <p class="text-white text-xl font-medium">Welcome, {{ $user->full_name }}!</p>
        <p class="text-sm font-medium text-white bg-accent p-2 rounded-xl w-fit px-4">{{ auth()->user()->department->name ?? 'N/A' }}</p>
    </article>

    <!-- Budget Request Statistics -->
    <div>
        <h2 class="text-xl font-semibold text-white mb-4">My Budget Requests</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Requests -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Total Requests</p>
                        <p class="text-3xl font-bold text-amber-500">{{ $totalBudgets }}</p>
                    </div>
                    <div class="bg-amber-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Pending</p>
                        <p class="text-3xl font-bold text-yellow-500">{{ $pendingRequests }}</p>
                    </div>
                    <div class="bg-yellow-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Approved Requests -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Approved</p>
                        <p class="text-3xl font-bold text-green-500">{{ $approvedRequests }}</p>
                    </div>
                    <div class="bg-green-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Rejected/Archived -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Rejected</p>
                        <p class="text-3xl font-bold text-red-500">{{ $rejectedRequests }}</p>
                    </div>
                    <div class="bg-red-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div>
        <h2 class="text-xl font-semibold text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Budget Submission -->
            <a href="{{ route('staff.budget.create') }}" class="bg-orange-brown hover:bg-primary transition-colors rounded-lg p-6 shadow-lg flex items-center space-x-4">
                <div class="bg-blue-500/20 rounded-full p-4">
                    <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Submit New Budget</h3>
                    <p class="text-sm text-gray-300">Create and submit a new budget request</p>
                </div>
            </a>

            <!-- Document Tracking -->
            <a href="{{ route('staff.document.tracking') }}" class="bg-orange-brown hover:bg-primary transition-colors rounded-lg p-6 shadow-lg flex items-center space-x-4">
                <div class="bg-amber-500/20 rounded-full p-4">
                    <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Track Documents</h3>
                    <p class="text-sm text-gray-300">View and track your budget requests</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Requests -->
    <div>
        <h2 class="text-xl font-semibold text-white mb-4">Recent Requests</h2>
        <div class="bg-orange-brown rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-primary">
                    <thead class="bg-primary">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Total Budget</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Submitted</th>
                        </tr>
                    </thead>
                    <tbody class="bg-orange-brown divide-y divide-primary">
                        @forelse($recentRequests as $budget)
                            <tr class="hover:bg-primary/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-medium">
                                    #{{ $budget->id }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="text-white font-medium max-w-xs truncate" title="{{ $budget->title }}">
                                        {{ $budget->title }}
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $budget->category }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    {{ $budget->department->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                    ₱{{ number_format($budget->total_budget, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-budget.status-badge :status="$budget->status" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    <div>{{ $budget->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $budget->created_at->format('h:i A') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-white font-medium">No budget requests yet</p>
                                        <p class="text-sm text-gray-400 mt-1">Start by submitting your first budget request</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection