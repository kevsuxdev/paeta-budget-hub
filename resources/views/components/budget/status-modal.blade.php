@props(['userRole'])

<div id="statusModal" class="modal-backdrop fixed inset-0 bg-transparent backdrop-blur-sm flex items-center justify-center hidden z-50" data-user-role="{{ $userRole }}">
    <div class="bg-orange-brown p-6 border border-white/60 w-96 shadow-lg rounded-md">
        <h3 class="text-xl font-semibold text-white mb-4">Update Budget Status</h3>

        <form id="statusForm" method="POST">
            @csrf
            <div class="mb-4">
                <label for="statusSelect" class="block text-sm font-medium text-white mb-2">
                    Select Status
                </label>
                <select
                    id="statusSelect"
                    name="status"
                    class="w-full bg-input border border-black/20 rounded-md p-2 text-sm"
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
                <label for="remarksTextarea" class="block text-sm font-medium text-white mb-2">
                    Remarks <span class="text-white text-xs" id="remarksRequired">(Optional)</span>
                </label>
                <textarea
                    id="remarksTextarea"
                    name="remarks"
                    rows="4"
                    maxlength="500"
                    class="w-full border bg-input border-black/20 rounded-md p-2 text-sm resize-none"
                    placeholder="Add your comments or remarks here..."
                ></textarea>
                <p class="text-xs text-white mt-1">Maximum 500 characters</p>
            </div>
            @endif

            <div class="flex justify-end gap-3">
                <button
                    type="button"
                    class="btn-close-modal px-4 py-2 bg-secondary rounded text-white hover:bg-primary/50 text-white text-sm"
                    data-modal-id="statusModal"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-primary text-white rounded hover:bg-primary/80 text-sm"
                >
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>
