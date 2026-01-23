<div class="bg-orange-brown p-4 rounded-lg">
    <h4 class="font-semibold text-white mb-3">Request Process</h4>
    <div class="space-y-3">
        <!-- Step 1: Request Submitted -->
        <div class="flex items-center space-x-3">
            <div class="shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-bold">1</span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-white">Request Submitted</p>
                <p class="text-sm text-gray-300" id="submittedDate"></p>
            </div>
            <div class="shrink-0">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Completed
                </span>
            </div>
        </div>

        <!-- Step 2: Department Head Review -->
        <div class="flex items-center space-x-3">
            <div class="shrink-0 w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-bold">2</span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-white">Department Head Review</p>
                <p class="text-sm text-white" id="deptHeadDate"></p>
            </div>
            <div class="shrink-0">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" id="deptHeadStatus">
                    Pending
                </span>
            </div>
        </div>

        <!-- Step 3: Finance Review -->
        <div class="flex items-center space-x-3">
            <div class="shrink-0 w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-bold">3</span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-white">Finance Review</p>
                <p class="text-sm text-white" id="financeDate"></p>
            </div>
            <div class="shrink-0">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" id="financeStatus">
                    Pending
                </span>
            </div>
        </div>

        <!-- Step 4: Final Approval -->
        <div class="flex items-center space-x-3">
            <div class="shrink-0 w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-bold">4</span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-white">Budget Final Approval</p>
                <p class="text-sm text-white" id="finalDate"></p>
            </div>
            <div class="shrink-0">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" id="finalStatus">
                    Pending
                </span>
            </div>
        </div>
    </div>
</div>
