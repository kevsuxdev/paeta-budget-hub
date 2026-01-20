@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-primary mb-4">Document Tracking</h1>
    <p class="text-gray-600 mb-6">Track and manage all budget requests with search and filter capabilities.</p>

    <!-- Search and Filter -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
        <form method="GET" action="{{ route('admin.document.tracking') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by title or user name" class="w-full border border-black/20 rounded-md p-2 text-sm">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                <select name="status" id="status" class="w-full border border-black/20 rounded-md p-2 text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90">Search</button>
            </div>
        </form>
    </div>

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
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $budget->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $budget->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $budget->user->full_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $budget->department->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($budget->total_budget, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span @class([ 'inline-flex px-2 py-1 text-xs font-semibold rounded-full' , 'bg-yellow-100 text-yellow-800'=> $budget->status === 'pending',
                                'bg-green-100 text-green-800' => $budget->status === 'approved',
                                'bg-red-100 text-red-800' => $budget->status === 'rejected',
                                ])>
                                {{ ucfirst($budget->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $budget->submission_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <x-button type="button"
                                onclick="openModalFromButton(this)"
                                data-id="{{ $budget->id }}"
                                data-title="{{ $budget->title }}"
                                data-status="{{ $budget->status }}"
                                data-date="{{ optional($budget->submission_date)->format('M d, Y') }}"
                                data-user="{{ optional($budget->user)->full_name ?? 'N/A' }}">
                                View Details
                            </x-button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No budget requests found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($budgets->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $budgets->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Budget Details Modal -->
    <div id="budgetModal" class="fixed inset-0 bg-transparent backdrop-blur-sm flex items-center justify-center hidden">
        <div class="bg-white p-6 border border-black/20 w-full max-w-4xl shadow-lg rounded-md max-h-[90vh] overflow-y-auto">
            <div class="mb-4">
                <h3 class="text-xl font-semibold text-gray-900" id="modalTitle"></h3>
                <p class="text-sm text-gray-600">Budget Request Process Timeline</p>
            </div>
            <div class="space-y-6">
                <!-- Process Steps -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-900 mb-3">Request Process</h4>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-bold">1</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Request Submitted</p>
                                <p class="text-sm text-gray-600" id="submittedDate"></p>
                            </div>
                            <div class="shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Completed
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="shrink-0 w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-bold">2</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Department Head Review</p>
                                <p class="text-sm text-gray-600" id="deptHeadDate"></p>
                            </div>
                            <div class="shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" id="deptHeadStatus">
                                    Pending
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="shrink-0 w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-bold">3</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Finance Review</p>
                                <p class="text-sm text-gray-600" id="financeDate"></p>
                            </div>
                            <div class="shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" id="financeStatus">
                                    Pending
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="shrink-0 w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-bold">4</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Budget Final Approval</p>
                                <p class="text-sm text-gray-600" id="finalDate"></p>
                            </div>
                            <div class="shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" id="finalStatus">
                                    Pending
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Budget Details -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-900 mb-3">Budget Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium">Requester:</span>
                            <span id="requesterName"></span>
                        </div>
                        <div>
                            <span class="font-medium">Title:</span>
                            <span id="budgetTitle"></span>
                        </div>
                        <div>
                            <span class="font-medium">Fiscal Year:</span>
                            <span id="budgetFiscalYear"></span>
                        </div>
                        <div>
                            <span class="font-medium">Category:</span>
                            <span id="budgetCategory"></span>
                        </div>
                        <div>
                            <span class="font-medium">Total Budget:</span>
                            <span id="budgetTotal"></span>
                        </div>
                        <div class="md:col-span-2">
                            <span class="font-medium">Justification:</span>
                            <span id="budgetJustification"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-900 rounded-md hover:bg-gray-400 transition-colors">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openModalFromButton(button) {
        const id = button.getAttribute('data-id');
        const title = button.getAttribute('data-title');
        const status = button.getAttribute('data-status');
        const submissionDate = button.getAttribute('data-date');
        const requesterName = button.getAttribute('data-user');

        openModal(id, title, status, submissionDate, requesterName);
    }

    function openModal(id, title, status, submissionDate, requesterName) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('submittedDate').textContent = submissionDate;

        const deptHeadStatus = document.getElementById('deptHeadStatus');
        const financeStatus = document.getElementById('financeStatus');
        const finalStatus = document.getElementById('finalStatus');
        const deptHeadDate = document.getElementById('deptHeadDate');
        const financeDate = document.getElementById('financeDate');
        const finalDate = document.getElementById('finalDate');

        // Reset all
        deptHeadStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
        deptHeadStatus.textContent = 'Pending';
        financeStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
        financeStatus.textContent = 'Pending';
        finalStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
        finalStatus.textContent = 'Pending';
        deptHeadDate.textContent = '';
        financeDate.textContent = '';
        finalDate.textContent = '';

        if (status === 'pending') {
            deptHeadStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800';
            deptHeadStatus.textContent = 'Current';
            deptHeadDate.textContent = submissionDate;
        } else if (status === 'approved' || status === 'rejected') {
            deptHeadStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
            deptHeadStatus.textContent = 'Completed';
            financeStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
            financeStatus.textContent = 'Completed';
            finalStatus.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
            finalStatus.textContent = status === 'approved' ? 'Approved' : 'Rejected';
            deptHeadDate.textContent = submissionDate;
            financeDate.textContent = submissionDate;
            finalDate.textContent = submissionDate; // Placeholder
        }

        // Set budget details
        document.getElementById('requesterName').textContent = requesterName;
        document.getElementById('budgetTitle').textContent = title;
        document.getElementById('budgetFiscalYear').textContent = '2024'; // Placeholder
        document.getElementById('budgetCategory').textContent = 'General'; // Placeholder
        document.getElementById('budgetTotal').textContent = '$10,000'; // Placeholder
        document.getElementById('budgetJustification').textContent = 'Budget justification here'; // Placeholder

        document.getElementById('budgetModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('budgetModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('budgetModal');
        if (event.target == modal) {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection