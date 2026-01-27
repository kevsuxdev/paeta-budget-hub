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
        <h1 class="text-2xl font-bold text-black">Finance Dashboard</h1>
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
        <p class="text-black text-xl font-medium">Welcome, Finance Officer!</p>
        <p class="text-sm font-medium text-white bg-accent p-2 rounded-xl w-fit px-4">Finance Department</p>
    </article>

    <!-- Finance Budget Statistics -->
    <div>
        <h2 class="text-xl font-semibold text-white mb-4">Budget Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Requests -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Total Requests</p>
                        <p class="text-3xl font-bold text-amber-500">{{ $totalRequests ?? '-' }}</p>
                    </div>
                    <div class="bg-amber-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <!-- Pending -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Pending</p>
                        <p class="text-3xl font-bold text-yellow-500">{{ $pendingReview }}</p>
                    </div>
                    <div class="bg-yellow-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <!-- Approved -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Approved</p>
                        <p class="text-3xl font-bold text-green-500">{{ $approvedCount ?? '-' }}</p>
                    </div>
                    <div class="bg-green-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <!-- Rejected -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Rejected</p>
                        <p class="text-3xl font-bold text-red-500">{{ $rejectedCount ?? '-' }}</p>
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
    <div class="flex items-start w-full gap-2">
        <!-- Department Budget Pie Chart -->
        <div class="bg-orange-brown rounded-lg shadow-lg p-6 w-full">
            <h2 class="text-xl font-semibold text-white mb-4">Approved Budget by Department</h2>
            <div class="flex justify-center">
                <canvas id="departmentBudgetChart" style="max-width: 500px; max-height: 500px;"></canvas>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-orange-brown p-6 col-span-1 rounded-lg text-nowrap shadow-sm border border-orange-brown">
            <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('finance.review') }}" class="flex items-center px-4 py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Review Request
                </a>

                <a href="{{ route('finance.approval') }}" class="flex items-center px-4 py-3 bg-secondary text-white rounded-lg hover:bg-secondary/50 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Final Approval
                </a>

                <a href="{{ route('finance.audit.trail') }}" class="flex items-center px-4 py-3 bg-secondary text-white rounded-lg hover:bg-secondary/50 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Audit Trail
                </a>

                <a href="{{ route('finance.archive') }}" class="flex items-center px-4 py-3 bg-secondary text-white rounded-lg hover:bg-secondary/50 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    View Archive
                </a>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('departmentBudgetChart').getContext('2d');
    const departmentData = JSON.parse('{!! json_encode($departmentBudgets) !!}');
    const departmentLabels = departmentData.map(dept => dept.department_name);
    const departmentValues = departmentData.map(dept => dept.total);

    // Generate colors for each department
    const colors = [
        'rgba(245, 158, 11, 0.8)', // amber
        'rgba(249, 115, 22, 0.8)', // orange
        'rgba(34, 197, 94, 0.8)', // green
        'rgba(59, 130, 246, 0.8)', // blue
        'rgba(239, 68, 68, 0.8)', // red
        'rgba(168, 85, 247, 0.8)', // purple
        'rgba(236, 72, 153, 0.8)', // pink
        'rgba(20, 184, 166, 0.8)', // teal
    ];

    const borderColors = [
        'rgba(245, 158, 11, 1)',
        'rgba(249, 115, 22, 1)',
        'rgba(34, 197, 94, 1)',
        'rgba(59, 130, 246, 1)',
        'rgba(239, 68, 68, 1)',
        'rgba(168, 85, 247, 1)',
        'rgba(236, 72, 153, 1)',
        'rgba(20, 184, 166, 1)',
    ];

    const departmentBudgetChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: departmentLabels,
            datasets: [{
                label: 'Approved Budget (₱)',
                data: departmentValues,
                backgroundColor: colors.slice(0, departmentLabels.length),
                borderColor: borderColors.slice(0, departmentLabels.length),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        color: '#ffffff',
                        font: {
                            size: 12
                        }
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
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed !== null) {
                                label += '₱' + context.parsed.toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }

                            // Calculate percentage
                            const total = context.dataset.data.reduce((a, b) => Number(a) + Number(b), 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            label += ` (${percentage}%)`;

                            return label;
                        }
                    }
                }
            }
        }
    });
</script>

@endsection