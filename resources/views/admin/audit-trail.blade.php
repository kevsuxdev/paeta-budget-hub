@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <h1 class="text-3xl font-bold text-black">Audit Trail</h1>
    <p class="text-black font-medium mb-6">Track all budget activities and system events in real-time.</p>

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
        <h2 class="text-xl font-semibold text-white mb-4">System Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Approved -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Total Approved</p>
                        <p class="text-3xl font-bold text-green-500">{{ $totalApproved }}</p>
                    </div>
                    <div class="bg-green-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Activities -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Total Activities</p>
                        <p class="text-3xl font-bold text-blue-500">{{ $totalActivities }}</p>
                    </div>
                    <div class="bg-blue-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Budgets Submitted -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Budgets Submitted</p>
                        <p class="text-3xl font-bold text-amber-500">{{ $totalBudgetsSubmitted }}</p>
                    </div>
                    <div class="bg-amber-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Active Users</p>
                        <p class="text-3xl font-bold text-orange-500">{{ $activeUsers }}</p>
                    </div>
                    <div class="bg-orange-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Activity Logs -->
    <div class="bg-orange-brown rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-primary">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-white">Activity Logs</h3>
                    <p class="text-sm text-gray-300 mt-1">Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} activities</p>
                </div>

                <!-- Search Form -->
                <form action="{{ route('admin.audit.trail') }}" method="GET" class="flex gap-2">
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Search by budget title..."
                            class="w-full text-sm md:w-80 pl-10 pr-4 py-2 bg-primary border border-gray-600 rounded-lg text-white placeholder-gray-400 ">
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm bg-amber-600 text-white rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-colors">
                        Search
                    </button>
                    @if($search)
                    <a
                        href="{{ route('admin.audit.trail') }}"
                        class="px-4 py-2 text-sm bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                        Clear
                    </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Activity Logs List -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-primary">
                <thead class="bg-primary">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Timestamp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status Change</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-orange-brown divide-y divide-primary">
                    @forelse($logs as $log)
                    <tr class="hover:bg-primary/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <div>{{ $log->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->created_at->format('h:i A') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($log->budget)
                            <div class="text-white font-medium whitespace-nowrap">{{ $log->budget->title }}</div>
                            <div class="text-xs text-gray-400 whitespace-nowrap">ID: {{ $log->budget->id }}</div>
                            @else
                            <span class="text-white italic">Not Applicable</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($log->user)
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-amber-600 flex items-center justify-center text-white font-semibold mr-2">
                                    {{ substr($log->user->full_name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-white">{{ $log->user->full_name }}</div>
                                    <div class="text-xs text-gray-400">{{ ucfirst($log->user->role) }}</div>
                                </div>
                            </div>
                            @else
                            <span class="text-gray-400 italic">System</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                            $actionColors = [
                            'created' => 'bg-blue-500/20 text-blue-400',
                            'updated' => 'bg-yellow-500/20 text-yellow-400',
                            'approved' => 'bg-green-500/20 text-green-400',
                            'rejected' => 'bg-red-500/20 text-red-400',
                            'dept_head_reviewed' => 'bg-amber-500/20 text-amber-400',
                            'finance_reviewed' => 'bg-orange-700/20 text-orange-300',
                            'final_approval' => 'bg-green-500/20 text-green-400',
                            'final_rejection' => 'bg-red-500/20 text-red-400',
                            'revision_requested' => 'bg-orange-500/20 text-orange-400',
                            ];
                            $colorClass = $actionColors[$log->action] ?? 'bg-gray-500/20 text-gray-400';
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                {{ ucwords(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-white">
                            @if($log->old_status || $log->new_status)
                            <div class="flex items-center space-x-2">
                                @if($log->old_status)
                                <span class="px-2 py-1 bg-gray-600 text-gray-300 rounded text-xs">
                                    {{ ucfirst($log->old_status) }}
                                </span>
                                @endif
                                @if($log->old_status && $log->new_status)
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                                @endif
                                @if($log->new_status)
                                @php
                                $statusColors = [
                                'pending' => 'bg-yellow-500/20 text-yellow-400',
                                'approved' => 'bg-green-500/20 text-green-400',
                                'rejected' => 'bg-red-500/20 text-red-400',
                                'under_review' => 'bg-blue-500/20 text-blue-400',
                                'revision_needed' => 'bg-orange-500/20 text-orange-400',
                                ];
                                $statusColor = $statusColors[$log->new_status] ?? 'bg-gray-500/20 text-gray-400';
                                @endphp
                                <span class="px-2 py-1 rounded text-xs {{ $statusColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $log->new_status)) }}
                                </span>
                                @endif
                            </div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-white">
                            <div class="whitespace-wrap" title="{{ $log->notes }}">
                                {{ $log->notes ?? '-' }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                @if($search)
                                <p class="text-lg font-medium text-white mb-1">No activities found</p>
                                <p class="text-gray-400">Try adjusting your search criteria.</p>
                                @else
                                <p class="text-lg font-medium text-white mb-1">No activities logged yet</p>
                                <p class="text-gray-400">Activity logs will appear here when actions are performed.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-primary">
            {{ $logs->appends(['search' => $search])->links() }}
        </div>
        @endif
    </div>
</div>
@endsection