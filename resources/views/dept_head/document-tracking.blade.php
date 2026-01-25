@extends('layouts.auth-layout')

@section('main-content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-primary mb-4">Document Tracking</h1>
    <p class="text-gray-600 mb-6">Track and manage budget requests from your department.</p>

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
        :route="route('dept_head.document.tracking')"
        :searchValue="request('search', '')"
        :statusValue="request('status', '')"
    />

    <!-- Budget Requests Table -->
    <div class="bg-orange-brown rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
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
                            :canUpdateStatus="true"
                        />
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                No budget requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($budgets->hasPages())
        <div class="mt-6">
            {{ $budgets->links() }}
        </div>
    @endif
</div>

<!-- Budget Details Modal -->
<x-budget.details-modal />

<!-- Status Update Modal -->
<x-budget.status-modal userRole="dept_head" />

<!-- JavaScript -->
<script src="{{ asset('js/budget-tracking.js') }}"></script>

 <!-- Edit Budget Modal -->
 <div id="editBudgetModal" class="modal-backdrop fixed inset-0 bg-transparent backdrop-blur-sm flex items-center justify-center hidden z-50">
     <div class="bg-primary p-6 border border-black/20 w-full max-w-3xl shadow-lg rounded-md max-h-[90vh] overflow-y-auto">
         <h3 class="text-xl font-semibold text-white mb-4">Edit Budget</h3>

         <form id="editBudgetForm" method="POST" enctype="multipart/form-data">
             @csrf
             @method('PUT')

             <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                 <div>
                     <label class="text-sm text-white">Title</label>
                     <input id="edit_title" name="title" type="text" class="w-full p-2 rounded" required />
                 </div>
                 <div>
                     <label class="text-sm text-white">Fiscal Year</label>
                     <input id="edit_fiscal_year" name="fiscal_year" type="number" class="w-full p-2 rounded" required />
                 </div>
                 <div>
                     <label class="text-sm text-white">Category</label>
                     <input id="edit_category" name="category" type="text" class="w-full p-2 rounded" required />
                 </div>
                 <div>
                     <label class="text-sm text-white">Submission Date</label>
                     <input id="edit_submission_date" name="submission_date" type="date" class="w-full p-2 rounded" min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" required />
                 </div>
             </div>

             <div class="mb-4">
                 <label class="text-sm text-white">Justification</label>
                 <textarea id="edit_justification" name="justification" rows="3" class="w-full p-2 rounded"></textarea>
             </div>

             <div class="bg-orange-brown p-4 rounded mb-4">
                 <h4 class="text-white mb-3">Line Items</h4>
                 <div id="edit-line-items" class="space-y-3"></div>
                 <div class="mt-3">
                     <button type="button" id="edit-add-item" class="bg-primary text-white px-3 py-2 rounded">Add Line Item</button>
                 </div>
             </div>

             <div class="flex justify-end gap-3">
                 <button type="button" class="btn-close-modal px-4 py-2 bg-gray-300 text-gray-900 rounded-md" data-modal-id="editBudgetModal">Cancel</button>
                 <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md">Save Changes</button>
             </div>
         </form>
     </div>
 </div>
@endsection
