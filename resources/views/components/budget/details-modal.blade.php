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

            <!-- Supporting Document & E-Signature -->
            <div class="bg-orange-brown p-4 rounded-lg">
                <div class="flex flex-col items-start md:space-x-8 space-y-2 md:space-y-0">
                    <div class="flex items-start flex-col gap-5">
                        <span class="font-semibold text-white">Supporting Document:</span>
                        <span id="supportingDocumentLink" class="ml-2 text-blue-200 underline"></span>
                        <span id="supportingDocumentFile" class="ml-2"></span>
                    </div>
                    <div id="eSignatureSection" class="hidden flex items-start flex-col gap-5">
                        <span class="font-semibold text-white">E-Signature:</span>
                        <span id="eSignatureLink" class="ml-2"></span>
                        <span id="eSignatureFile" class="ml-2"></span>
                    </div>
                </div>
            </div>

            <!-- Budget timeline -->
            <div class="bg-orange-brown p-4 rounded-lg">
                <h4 class="font-semibold text-white mb-3">Budget Line Items</h4>
                <div id="budgetLineItemsContainer">
                    <table class="min-w-full divide-y divide-primary bg-orange-brown rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Description</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Quantity</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Unit Cost</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Total Cost</th>
                            </tr>
                        </thead>
                        <tbody id="budgetLineItemsBody">
                            <tr><td colspan="4" class="text-center text-white py-2">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <!-- Activity Logs -->
            <x-budget.activity-logs />
        </div>

        <div class="flex justify-end mt-6">
            <button
                type="button"
                class="btn-close-modal px-4 py-2 bg-gray-300 text-gray-900 rounded-md hover:bg-gray-400 transition-colors"
                data-modal-id="budgetModal">
                Close
            </button>
        </div>
    </div>
</div>