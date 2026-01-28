@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <h1 class="text-3xl font-bold text-black">Final Approval</h1>
    <p class="text-black font-medium mb-6">Review and give final approval to budgets that have passed finance review.</p>

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
    <!-- Department Filter -->
    @if(isset($departments) && $departments->count())
    <div class="mb-4">
        <form method="GET" class="flex items-center gap-2">
            <label for="department_id" class="text-white sr-only">Department</label>
            <select name="department_id" id="department_id" class="rounded-md p-2 bg-orange-brown text-white border border-primary">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Filter</button>
            <a href="{{ url()->current() }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Clear</a>
        </form>
    </div>
    @endif

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
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Due Date</th>
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
                                    type="button"
                                    class="btn-view-budget px-3 py-1 text-sm text-black-700 bg-blue-100 rounded hover:bg-blue-200"
                                    data-budget-id="{{ $budget->id }}"
                                    data-budget-title="{{ $budget->title }}"
                                    data-budget-status="{{ $budget->status }}"
                                    data-budget-date="{{ \Carbon\Carbon::parse($budget->submission_date)->format('M d, Y') }}"
                                    data-budget-user="{{ $budget->user->full_name }}"
                                    >
                                    View Details
                                </button>
                                <span class="text-gray-300">|</span>
                                <button
                                    type="button"
                                    class="btn-approve-budget px-3 py-1 text-sm text-black-700 bg-green-100 rounded hover:bg-green-200"
                                    data-budget-id="{{ $budget->id }}"
                                    data-budget-title="{{ $budget->title }}"
                                >
                                    Approve
                                </button>
                                <span class="text-gray-300">|</span>
                                <form action="{{ route('finance.budget.finalReject', $budget->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="px-3 py-1 text-sm text-black-700 bg-red-100 rounded hover:bg-red-200"
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
                {{ $budgets->appends(request()->except('page'))->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="fixed inset-0 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5  w-full max-w-2xl shadow-lg rounded-md bg-orange-brown">
        <div class="flex justify-between items-center pb-3 border-b border-primary">
            <h3 class="text-xl font-semibold text-white">Final Budget Approval</h3>
            <button type="button" class="text-gray-300 hover:text-white" onclick="closeApprovalModal()">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="approvalForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mt-4 space-y-4">
                <div>
                    <p class="text-sm text-white mb-4">Budget: <span id="modalBudgetTitle" class="font-semibold"></span></p>
                </div>

                <div>
                    <label for="approver_name" class="block text-sm font-medium text-white mb-2">
                        Full Name of Approver <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="approver_name"
                        name="approver_name"
                        required
                        class="w-full px-3 py-2 bg-primary border border-gray-600 text-white rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                        placeholder="Enter your full name"
                    >
                </div>

                @php $user = request()->user(); @endphp
                <div>
                    <label class="block text-sm font-medium text-white mb-2">
                        E-Signature <span class="text-red-400">*</span>
                    </label>
                    @if(empty($user->e_signed))
                        <input
                            type="file"
                            id="e_signature"
                            name="e_signature"
                            required
                            accept="image/*"
                            class="w-full px-3 py-2 bg-primary border border-gray-600 text-white rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                        >
                        <p class="mt-1 text-sm text-gray-300">Upload your signature image (PNG, JPG, etc.)</p>
                    @else
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('storage/' . $user->e_signed) }}" alt="E-Signature" class="h-16 border rounded bg-white p-1">
                            <span class="text-green-300 text-sm">Signature on file</span>
                        </div>
                    @endif
                </div>

                <div class="bg-primary rounded-lg p-4">
                    <p class="text-sm font-semibold text-white mb-2">Certification:</p>
                    <p class="text-sm text-white mb-2">By signing this document, you certify that:</p>
                    <ul class="list-disc list-inside text-sm text-white space-y-1 ml-2">
                        <li>You have reviewed all budget details</li>
                        <li>This approval is authorized and legitimate</li>
                        <li>This signature is legally binding</li>
                    </ul>
                </div>

                <div class="flex items-start">
                    <input
                        type="checkbox"
                        id="acknowledge"
                        name="acknowledge"
                        required
                        class="mt-1 h-4 w-4 text-purple-500 border-gray-600 rounded bg-primary"
                    >
                    <label for="acknowledge" class="ml-2 text-sm text-white">
                        I acknowledge and certify the above statements <span class="text-red-400">*</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button
                    type="button"
                    onclick="closeApprovalModal()"
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                >
                    Approve Budget
                </button>
            </div>
        </form>
    </div>
</div>

<x-budget.details-modal />

<script src="{{ asset('js/budget-tracking.js') }}"></script>
<script src="{{ asset('js/budget-tracking.js') }}"></script>
<script>
    $(document).ready(function() {
        BudgetTracking.init();

        // Handle approve button click
        $('.btn-approve-budget').on('click', function() {
            const budgetId = $(this).data('budget-id');
            const budgetTitle = $(this).data('budget-title');

            $('#modalBudgetTitle').text(budgetTitle);
            $('#approvalForm').attr('action', `/finance/budget/${budgetId}/final-approve`);

            $('#approvalModal').removeClass('hidden');
        });
    });

    function closeApprovalModal() {
        $('#approvalModal').addClass('hidden');
        $('#approvalForm')[0].reset();
    }

    // Close modal when clicking outside
    $('#approvalModal').on('click', function(e) {
        if (e.target.id === 'approvalModal') {
            closeApprovalModal();
        }
    });
</script>
@endsection
