@extends('layouts.auth-layout')
@section('main-content')

<div class="p-6 w-full">
    <article class="">
        <h1 class="text-4xl font-bold text-white">Admin Dashboard</h1>
        <p>Welcome to the LGU Budgeting Tracking System. As an admin, you have access to all features.</p>
    </article>

    <!-- Overview Statistics -->
    <div class="bg-orange-brown p-6 rounded-lg shadow-sm mt-4 ">
        <h2 class="text-xl font-semibold text-white mb-4">Overview Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Request Budgets -->
            <div class="flex items-center">
                <svg class="w-10 h-10 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-white">Total Requests</p>
                    <p class="text-2xl font-bold text-white">{{ $totalBudgets }}</p>
                </div>
            </div>

            <!-- Total Pending Requests -->
            <div class="flex items-center">
                <svg class="w-10 h-10 text-yellow-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-white">Pending</p>
                    <p class="text-2xl font-bold text-yellow-500">{{ $pendingRequests }}</p>
                </div>
            </div>

            <!-- Total Approved Projects -->
            <div class="flex items-center">
                <svg class="w-10 h-10 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-white">Approved</p>
                    <p class="text-2xl font-bold text-green-500">{{ $approvedProjects }}</p>
                </div>
            </div>

            <!-- Total Rejected Projects -->
            <div class="flex items-center">
                <svg class="w-10 h-10 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-white">Rejected</p>
                    <p class="text-2xl font-bold text-red-500">{{ $rejectedProjects }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <!-- Recent Budget Requests -->
        <div class="bg-orange-brown p-6 col-span-2 rounded-lg shadow-sm border border-orange-brown">
            <h3 class="text-lg font-semibold text-white mb-4">Recent Budget Requests</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-primary">
                    <thead class="bg-primary">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Department</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-orange-brown divide-y divide-primary">
                        @forelse($recentBudgets as $budget)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-white">{{ $budget->title }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-white">{{ $budget->department->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-budget.status-badge :status="$budget->status" />
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-white">{{ $budget->submission_date->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-center text-sm text-gray-500">No recent requests</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-orange-brown p-6 col-span-1 rounded-lg shadow-sm border border-orange-brown">
            <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.budget.create') }}" class="flex items-center px-4 py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Budget Request
                </a>
                <a href="{{ route('admin.document.tracking') }}" class="flex items-center px-4 py-3 bg-secondary text-white rounded-lg hover:bg-secondary/50 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Document Tracking
                </a>
                <a href="#" class="flex items-center px-4 py-3 bg-secondary text-white rounded-lg hover:bg-secondary/50 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    View Archive
                </a>
            </div>
        </div>
    </div>

    <!-- Department Budget Pie Chart -->
    <div class="bg-orange-brown rounded-lg shadow-lg p-6 mt-6">
        <h2 class="text-xl font-semibold text-white mb-4">Approved Budget by Department</h2>
        <div class="flex justify-center">
            <canvas id="departmentBudgetChart" style="max-width: 500px; max-height: 500px;"></canvas>
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
                            color: '#ffffff',
                            padding: 15,
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
</div>
@endsection