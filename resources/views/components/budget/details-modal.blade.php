<div id="budgetModal" class="modal-backdrop fixed inset-0 bg-transparent backdrop-blur-sm flex items-center justify-center hidden z-50">
    <div class="bg-primary p-6 border border-black/20 w-full max-w-4xl shadow-lg rounded-md max-h-[90vh] overflow-y-auto">
        <div class="mb-4">
            <h3 class="text-xl font-semibold text-white" id="modalTitle"></h3>
            <p class="text-sm text-gray-300">Budget Request Process Timeline</p>
        </div>

        <div class="space-y-6">
            <!-- Process Steps -->
            <x-budget.process-timeline />

            <!-- Budget Details -->
            <x-budget.details-section />

            <!-- Activity Logs -->
            <x-budget.activity-logs />
        </div>

        <div class="flex justify-end mt-6">
            <button
                type="button"
                class="btn-close-modal px-4 py-2 bg-gray-300 text-gray-900 rounded-md hover:bg-gray-400 transition-colors"
                data-modal-id="budgetModal"
            >
                Close
            </button>
        </div>
    </div>
</div>
