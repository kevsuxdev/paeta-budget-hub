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
    <div class="w-full flex items-center justify-between">
        <h1 class="text-2xl font-bold text-white mb-4">Dashboard</h1>
        <div class="relative">
            <button id="notificationBell" class="relative focus:outline-none" title="Notifications" onclick="toggleNotifications()">
                <svg class="w-7 h-7 cursor-pointer text-white" fill="yellow" stroke="black" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>
            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto border border-gray-200">
                <div class="p-4 border-b font-semibold text-gray-700">Notifications</div>
                <ul class="divide-y divide-gray-200">
                    @forelse($notifications as $notification)
                        <li class="p-4 hover:bg-gray-100 transition">
                            <div class="flex items-start gap-2">
                                <div class="shrink-0 mt-1">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm text-gray-800 font-medium">
                                        <span class="font-bold">{{ $notification->user->full_name ?? 'System' }}</span>
                                        <span class="ml-1">{{ $notification->action }}</span>
                                        <span class="ml-1 text-gray-500">on</span>
                                        <span class="ml-1 font-semibold">{{ $notification->budget->title ?? 'Budget' }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $notification->notes }}</div>
                                    <div class="text-xs text-gray-400 mt-1">{{ $notification->created_at->format('M d, Y h:i A') }}</div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="p-4 text-center text-gray-500">No notifications yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <script>
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('hidden');
        }
        // Optional: Hide dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const bell = document.getElementById('notificationBell');
            const dropdown = document.getElementById('notificationDropdown');
            if (!bell.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    <article class="space-y-2">
        <p class="text-white text-xl font-medium">Welcome, {{ $user->full_name }}!</p>
        <p class="text-sm font-medium text-white bg-accent p-2 rounded-xl w-fit px-4">{{ auth()->user()->department->name ?? 'N/A' }}</p>
    </article>

    <!-- Department Budget Statistics -->
    <div>
        <h2 class="text-xl font-semibold text-white mb-4">Department Budget Requests</h2>
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
            <!-- Department Total Budget -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Department Budget (₱)</p>
                        <p class="text-3xl font-bold text-indigo-400">{{ number_format($departmentTotal, 2) }}</p>
                    </div>
                    <div class="bg-indigo-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-3.314 0-6 1.79-6 4s2.686 4 6 4 6-1.79 6-4-2.686-4-6-4zM12 4v4m0 8v4" />
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
            <!-- Document Tracking -->
            <a href="{{ route('dept_head.document.tracking') }}" class="bg-orange-brown hover:bg-primary transition-colors rounded-lg p-6 shadow-lg flex items-center space-x-4">
                <div class="bg-amber-500/20 rounded-full p-4">
                    <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Track Documents</h3>
                    <p class="text-sm text-gray-300">Review and track department budget requests</p>
                </div>
            </a>

            <!-- Review Requests -->
            <a href="{{ route('dept_head.document.tracking') }}" class="bg-orange-brown hover:bg-primary transition-colors rounded-lg p-6 shadow-lg flex items-center space-x-4">
                <div class="bg-blue-500/20 rounded-full p-4">
                    <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Review & Approve</h3>
                    <p class="text-sm text-gray-300">Review pending budget requests</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Department Requests -->
    <div>
        <h2 class="text-xl font-semibold text-white mb-4">Recent Department Requests</h2>
        <div class="bg-orange-brown rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-primary">
                    <thead class="bg-primary">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Submitted By</th>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-amber-600 flex items-center justify-center text-white text-xs font-semibold mr-2">
                                            {{ substr($budget->user->full_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-white">{{ $budget->user->full_name }}</div>
                                            <div class="text-xs text-gray-400">{{ ucfirst($budget->user->role) }}</div>
                                        </div>
                                    </div>
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
                                        <p class="text-white font-medium">No budget requests in this department yet</p>
                                        <p class="text-sm text-gray-400 mt-1">Department budget requests will appear here</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Approved Budget Chart -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-white">Approved Budget by Month</h2>
            <form method="GET" action="{{ route('dept_head.dashboard') }}" class="flex items-center space-x-2">
                <label for="year" class="text-sm text-white">Year:</label>
                <select name="year" id="year" onchange="this.form.submit()" class="bg-orange-brown border border-primary text-white text-sm rounded-lg px-3 py-2 focus:ring-amber-500 focus:border-amber-500">
                    @if($availableYears->isEmpty())
                        <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                    @else
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    @endif
                </select>
            </form>
        </div>
        <div class="bg-orange-brown rounded-lg shadow-lg p-6">
            <canvas id="monthlyBudgetChart" class="w-full" style="max-height: 400px;"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('monthlyBudgetChart').getContext('2d');
    const chartData = JSON.parse('{!! json_encode($chartData) !!}');

    const monthlyBudgetChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Approved Budget (₱)',
                data: chartData,
                backgroundColor: 'rgba(245, 158, 11, 0.8)',
                borderColor: 'rgba(245, 158, 11, 1)',
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: 'rgba(245, 158, 11, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: '#ffffff',
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: 'rgba(245, 158, 11, 1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += '₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#ffffff',
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            return '₱' + value.toLocaleString('en-US');
                        }
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)',
                        drawBorder: false
                    }
                },
                x: {
                    ticks: {
                        color: '#ffffff',
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            }
        }
    });
</script>

@endsection