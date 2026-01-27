@props(['userRole'])

<div id="statusModal" class="modal-backdrop fixed inset-0 bg-transparent backdrop-blur-sm flex items-center justify-center hidden z-50" data-user-role="{{ $userRole }}">
    <div class="bg-white p-6 border border-black/20 w-full max-w-md shadow-lg rounded-md">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Update Budget Status</h3>

        <form id="statusForm" method="POST">
            @csrf
            <div class="mb-4">
                <label for="statusSelect" class="block text-sm font-medium text-gray-700 mb-2">
                    Select Status
                </label>
                <select
                    id="statusSelect"
                    name="status"
                    class="w-full border border-black/20 rounded-md p-2 text-sm"
                >
                    @if($userRole === 'dept_head')
                        <option value="reviewed">Reviewed</option>
                        <option value="rejected">Rejected</option>
                    @elseif($userRole === 'finance')
                        <option value="finance_reviewed">Reviewed</option>
                        <option value="revise">Revise</option>
                        <option value="rejected">Rejected</option>
                    @else
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    @endif
                </select>
            </div>

            @if($userRole === 'dept_head' || $userRole === 'finance')
            <div class="mb-4" id="remarksField">
                <label for="remarksTextarea" class="block text-sm font-medium text-gray-700 mb-2">
                    Remarks <span class="text-gray-500 text-xs" id="remarksRequired">(Optional)</span>
                </label>
                <textarea
                    id="remarksTextarea"
                    name="remarks"
                    rows="4"
                    maxlength="500"
                    class="w-full border border-black/20 rounded-md p-2 text-sm resize-none"
                    placeholder="Add your comments or remarks here..."
                ></textarea>
                <p class="text-xs text-gray-500 mt-1">Maximum 500 characters</p>
            </div>
            @endif

            <div class="flex justify-end gap-3">
                <button
                    type="button"
                    class="btn-close-modal px-4 py-2 bg-gray-300 text-gray-900 rounded-md hover:bg-gray-400 transition-colors"
                    data-modal-id="statusModal"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-opacity-90 transition-colors"
                >
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>
