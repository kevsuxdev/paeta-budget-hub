@props(['budget', 'canUpdateStatus' => false])

<tr>
    <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm text-white">{{ $budget->id }}</td>
    <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm text-white">{{ $budget->title }}</td>
    <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm text-white">{{ $budget->user->full_name ?? 'N/A' }}</td>
    <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm text-white">{{ $budget->department->name ?? 'N/A' }}</td>
    <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm text-white">{{ number_format($budget->total_budget, 2) }}</td>
    <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm text-white">
        <x-budget.status-badge :status="$budget->status" />
    </td>
    <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm text-white">{{ $budget->submission_date->format('M d, Y') }}</td>
    <td class="px-6 bg-orange-brown py-4 whitespace-nowrap text-sm font-medium">
        <div class="flex items-center gap-2">
            <x-button
                type="button"
                class="btn-view-budget"
                data-budget-id="{{ $budget->id }}"
                data-budget-title="{{ $budget->title }}"
                data-budget-status="{{ $budget->status }}"
                data-budget-date="{{ $budget->submission_date->format('M d, Y') }}"
                data-budget-user="{{ $budget->user->full_name ?? 'N/A' }}"
            >
                View Details
            </x-button>

            @if($canUpdateStatus)
                <button
                    type="button"
                    class="btn-update-status inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                    data-budget-id="{{ $budget->id }}"
                    data-budget-status="{{ $budget->status }}"
                >
                    Update Status
                </button>
            @endif
        </div>
    </td>
</tr>
