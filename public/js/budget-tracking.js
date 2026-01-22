/**
 * Budget Tracking Modal and Status Management
 * Uses jQuery and data attributes for cleaner, maintainable code
 */

$(document).ready(function() {
    // Initialize event handlers
    BudgetTracking.init();
});

// Main Budget Tracking Module
const BudgetTracking = {
    /**
     * Initialize all event handlers
     */
    init() {
        this.initViewDetailsButtons();
        this.initUpdateStatusButtons();
        this.initModalCloseHandlers();
    },

    /**
     * Initialize "View Details" button handlers
     */
    initViewDetailsButtons() {
        $(document).on('click', '.btn-view-budget', function(e) {
            e.preventDefault();
            const $btn = $(this);

            const budgetData = {
                id: $btn.data('budget-id'),
                title: $btn.data('budget-title'),
                status: $btn.data('budget-status'),
                date: $btn.data('budget-date'),
                user: $btn.data('budget-user')
            };

            BudgetModal.open(budgetData);
        });
    },

    /**
     * Initialize "Update Status" button handlers
     */
    initUpdateStatusButtons() {
        $(document).on('click', '.btn-update-status', function(e) {
            e.preventDefault();
            const $btn = $(this);

            const budgetId = $btn.data('budget-id');
            const currentStatus = $btn.data('budget-status');

            StatusModal.open(budgetId, currentStatus);
        });
    },

    /**
     * Initialize modal close handlers
     */
    initModalCloseHandlers() {
        // Close on button click
        $(document).on('click', '.btn-close-modal', function(e) {
            e.preventDefault();
            const modalId = $(this).data('modal-id');
            $(`#${modalId}`).addClass('hidden');
        });

        // Close on outside click
        $(document).on('click', '.modal-backdrop', function(e) {
            if (e.target === this) {
                $(this).addClass('hidden');
            }
        });

        // Close on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.modal-backdrop:not(.hidden)').addClass('hidden');
            }
        });
    }
};

// Modal Management
const BudgetModal = {
    /**
     * Open budget details modal
     */
    async open(budgetData) {
        this.setBasicInfo(budgetData);
        this.updateProcessTimeline(budgetData.status, budgetData.date);
        this.show();
        await this.fetchBudgetData(budgetData.id);
    },

    /**
     * Set basic modal information
     */
    setBasicInfo(budgetData) {
        $('#modalTitle').text(budgetData.title);
        $('#submittedDate').text(budgetData.date);
        $('#requesterName').text(budgetData.user);
        $('#budgetTitle').text(budgetData.title);
    },

    /**
     * Update process timeline based on budget status
     */
    updateProcessTimeline(status, submissionDate) {
        // Reset all statuses
        this.resetProcessSteps();

        // Update based on current status
        if (status === 'pending') {
            this.setPendingStatus(submissionDate);
        } else if (status === 'reviewed') {
            this.setReviewedStatus(submissionDate);
        } else if (status === 'finance_reviewed') {
            this.setFinanceReviewedStatus(submissionDate);
        } else if (status === 'approved' || status === 'rejected') {
            this.setCompletedStatus(status, submissionDate);
        }
    },

    /**
     * Reset all process steps to default
     */
    resetProcessSteps() {
        const defaultClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800';

        $('#deptHeadStatus').attr('class', defaultClass).text('Pending');
        $('#financeStatus').attr('class', defaultClass).text('Pending');
        $('#finalStatus').attr('class', defaultClass).text('Pending');

        $('#deptHeadDate, #financeDate, #finalDate').text('');
    },

    /**
     * Set pending status styling
     */
    setPendingStatus(submissionDate) {
        $('#deptHeadStatus')
            .attr('class', 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800')
            .text('Pending');
        $('#deptHeadDate').text(submissionDate);
    },

    /**
     * Set reviewed status styling
     */
    setReviewedStatus(submissionDate) {
        const completedClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';

        $('#deptHeadStatus').attr('class', completedClass).text('Completed');
        $('#deptHeadDate').text(submissionDate);

        $('#financeStatus')
            .attr('class', 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800')
            .text('Pending');
        $('#financeDate').text(submissionDate);
    },

    /**
     * Set finance reviewed status styling
     */
    setFinanceReviewedStatus(submissionDate) {
        const completedClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';

        $('#deptHeadStatus').attr('class', completedClass).text('Completed');
        $('#deptHeadDate').text(submissionDate);

        $('#financeStatus')
            .attr('class', 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800')
            .text('Finance Reviewed');
        $('#financeDate').text(submissionDate);
    },

    /**
     * Set completed status styling
     */
    setCompletedStatus(status, submissionDate) {
        const completedClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';

        $('#deptHeadStatus').attr('class', completedClass).text('Completed');
        $('#financeStatus').attr('class', completedClass).text('Completed');

        const finalClass = status === 'approved'
            ? 'bg-green-100 text-green-800'
            : 'bg-red-100 text-red-800';
        $('#finalStatus')
            .attr('class', `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${finalClass}`)
            .text(status === 'approved' ? 'Approved' : 'Rejected');

        $('#deptHeadDate, #financeDate, #finalDate').text(submissionDate);
    },

    /**
     * Fetch budget data from server using jQuery AJAX
     */
    async fetchBudgetData(budgetId) {
        try {
            // Determine the correct URL based on current page path
            const currentPath = window.location.pathname;
            let baseUrl = '/admin';

            if (currentPath.includes('/dept_head/')) {
                baseUrl = '/dept_head';
            } else if (currentPath.includes('/finance/')) {
                baseUrl = '/finance';
            } else if (currentPath.includes('/staff/')) {
                baseUrl = '/staff';
            } else if (currentPath.includes('/admin/')) {
                baseUrl = '/admin';
            }

            const data = await $.ajax({
                url: `${baseUrl}/budget/${budgetId}/logs`,
                method: 'GET',
                dataType: 'json'
            });

            this.updateBudgetDetails(data.budget);
            this.updateActivityLogs(data.logs);
        } catch (error) {
            console.error('Error fetching budget logs:', error);
            this.showActivityLogsError();
        }
    },

    /**
     * Update budget details section
     */
    updateBudgetDetails(budget) {
        $('#budgetFiscalYear').text(budget.fiscal_year);
        $('#budgetCategory').text(budget.category);
        $('#budgetTotal').text('$' + budget.total_budget);
        $('#budgetJustification').text(budget.justification || 'N/A');
    },

    /**
     * Update activity logs section
     */
    updateActivityLogs(logs) {
        const $activityLogs = $('#activityLogs');

        if (!logs || logs.length === 0) {
            $activityLogs.html('<div class="text-center text-sm text-gray-500 py-4">No activity logs found</div>');
            return;
        }

        const logsHtml = logs.map(log => this.createLogEntry(log)).join('');
        $activityLogs.html(logsHtml);
    },

    /**
     * Create HTML for a single log entry
     */
    createLogEntry(log) {
        const logConfig = this.getLogConfig(log);

        return `
            <div class="flex items-start space-x-3 p-3 bg-white rounded-md border border-gray-200">
                <div class="text-2xl">${logConfig.icon}</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium ${logConfig.color}">${logConfig.text}</p>
                    <p class="text-sm text-gray-600 mt-1">${this.escapeHtml(log.notes || 'No notes')}</p>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-xs text-gray-500">
                        <span><strong>User:</strong> ${this.escapeHtml(log.user_name)}</span>
                        <span><strong>Department:</strong> ${this.escapeHtml(log.department_name)}</span>
                        <span><strong>Time:</strong> ${this.escapeHtml(log.timestamp)}</span>
                    </div>
                </div>
            </div>
        `;
    },

    /**
     * Get log configuration based on action/status
     */
    getLogConfig(log) {
        if (log.action === 'created') {
            return { icon: '📝', color: 'text-blue-600', text: 'Budget Created' };
        } else if (log.new_status === 'approved') {
            return { icon: '✅', color: 'text-green-600', text: 'Status Changed to Approved' };
        } else if (log.new_status === 'rejected') {
            return { icon: '❌', color: 'text-red-600', text: 'Status Changed to Rejected' };
        } else {
            return { icon: '🔄', color: 'text-yellow-600', text: 'Status Updated' };
        }
    },

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    /**
     * Show error message in activity logs
     */
    showActivityLogsError() {
        $('#activityLogs').html(
            '<div class="text-center text-sm text-red-500 py-4">Error loading activity logs</div>'
        );
    },

    /**
     * Show the modal
     */
    show() {
        $('#budgetModal').removeClass('hidden');
    },

    /**
     * Close the modal
     */
    close() {
        $('#budgetModal').addClass('hidden');
    }
};

// Status Modal Management
const StatusModal = {
    /**
     * Open status update modal
     */
    open(budgetId, currentStatus) {
        const formAction = this.getFormAction(budgetId);

        $('#statusForm').attr('action', formAction);
        $('#statusSelect').val(currentStatus);
        $('#statusModal').removeClass('hidden');
    },

    /**
     * Get form action URL based on current page path
     */
    getFormAction(budgetId) {
        const currentPath = window.location.pathname;
        let baseUrl = '/admin';

        if (currentPath.includes('/dept_head/')) {
            baseUrl = '/dept_head';
        } else if (currentPath.includes('/finance/')) {
            baseUrl = '/finance';
        } else if (currentPath.includes('/admin/')) {
            baseUrl = '/admin';
        }

        return `${baseUrl}/budget/${budgetId}/update-status`;
    },

    /**
     * Close the modal
     */
    close() {
        $('#statusModal').addClass('hidden');
    }
};
