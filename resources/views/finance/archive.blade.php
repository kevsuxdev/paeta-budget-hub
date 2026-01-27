@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <h1 class="text-3xl font-bold text-black">Budget Archive</h1>
    <p class="text-black font-medium mb-6">View and manage archived budget documents (approved and rejected).</p>

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
        <h2 class="text-xl font-semibold text-white mb-4">Archive Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Archived -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Total Archived</p>
                        <p class="text-3xl font-bold text-amber-500">{{ $totalArchived }}</p>
                        <p class="text-xs text-gray-400 mt-1">Approved + Rejected</p>
                    </div>
                    <div class="bg-amber-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Approved Budgets -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Approved Budgets</p>
                        <p class="text-3xl font-bold text-green-500">{{ $approvedBudgets }}</p>
                        <p class="text-xs text-gray-400 mt-1">Successfully approved</p>
                    </div>
                    <div class="bg-green-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Value -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Total Value</p>
                        <p class="text-3xl font-bold text-blue-500">{{ $formattedTotalValue }}</p>
                        <p class="text-xs text-gray-400 mt-1">₱{{ number_format($totalValue, 2) }}</p>
                    </div>
                    <div class="bg-blue-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Departments -->
            <div class="bg-orange-brown rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white mb-1">Total Departments</p>
                        <p class="text-3xl font-bold text-orange-500">{{ $totalDepartments }}</p>
                        <p class="text-xs text-gray-400 mt-1">Across organization</p>
                    </div>
                    <div class="bg-orange-500/20 rounded-full p-3">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-orange-brown rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-primary">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-white">Archived Documents</h3>
                    <p class="text-sm text-gray-300 mt-1">Showing {{ $budgets->firstItem() ?? 0 }} to {{ $budgets->lastItem() ?? 0 }} of {{ $budgets->total() }} documents</p>
                </div>

                <!-- Search and Filter Form -->
                <form action="{{ route('finance.archive') }}" method="GET" class="flex flex-col md:flex-row gap-2">
                    <!-- Search Input -->
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Search by ID, title, or department..."
                            class="w-full text-sm md:w-80 pl-10 pr-4 py-2 bg-primary border border-gray-600 rounded-lg text-white placeholder-gray-400 "
                        >
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <!-- Status Filter -->
                    <select
                        name="status"
                        class="px-4 py-2 text-sm bg-primary border border-gray-600 rounded-lg text-white"
                    >
                        <option value="">All Status</option>
                        <option value="approved" {{ $statusFilter == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $statusFilter == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>

                    <!-- Action Buttons -->
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm cursor-pointer bg-amber-600 text-white rounded-lg hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-colors"
                    >
                        Search
                    </button>
                    @if($search || $statusFilter)
                        <a
                            href="{{ route('finance.archive') }}"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors text-center"
                        >
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Archived Documents List -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-primary">
                <thead class="bg-primary">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Total Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Fiscal Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Archived Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-orange-brown divide-y divide-primary">
                    @forelse($budgets as $budget)
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
                                    <div class="h-8 w-8 rounded-full bg-orange-600 flex items-center justify-center text-white text-xs font-semibold mr-2">
                                        {{ substr($budget->department->name, 0, 2) }}
                                    </div>
                                    <div class="text-white">{{ $budget->department->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                <div>{{ $budget->user->full_name }}</div>
                                <div class="text-xs text-gray-400">{{ ucfirst($budget->user->role) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                ₱{{ number_format($budget->total_budget, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-budget.status-badge :status="$budget->status" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                {{ $budget->fiscal_year }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                <div>{{ $budget->updated_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $budget->updated_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <button
                                    class="btn-view-budget text-blue-400 hover:text-blue-300 font-medium"
                                    data-budget-id="{{ $budget->id }}"
                                    data-budget-title="{{ $budget->title }}"
                                    data-budget-status="{{ $budget->status }}"
                                    data-budget-date="{{ $budget->submission_date->format('M d, Y') }}"
                                    data-budget-user="{{ $budget->user->full_name }}"
                                >
                                    View Details
                                </button>
                                <span class="text-gray-500">|</span>
                                <a
                                    href="{{ route('finance.budget.downloadPdf', $budget->id) }}"
                                    class="text-red-400 hover:text-red-300 font-medium"
                                    target="_blank"
                                >
                                    Download PDF
                                </a>
                                @if($budget->supporting_document)
                                    <span class="text-gray-500">|</span>
                                    <a
                                        href="{{ asset('storage/' . $budget->supporting_document) }}"
                                        download="document_budget_{{ $budget->id }}.{{ pathinfo($budget->supporting_document, PATHINFO_EXTENSION) }}"
                                        target="_blank"
                                        class="text-amber-400 hover:text-amber-300 font-medium"
                                    >
                                        Document
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                    </svg>
                                    @if($search || $statusFilter)
                                        <p class="text-lg font-medium text-white mb-1">No archived documents found</p>
                                        <p class="text-gray-400">Try adjusting your search or filter criteria.</p>
                                    @else
                                        <p class="text-lg font-medium text-white mb-1">No archived documents</p>
                                        <p class="text-gray-400">Approved and rejected budgets will appear here.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($budgets->hasPages())
            <div class="px-6 py-4 border-t border-primary">
                {{ $budgets->appends(['search' => $search, 'status' => $statusFilter])->links() }}
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
