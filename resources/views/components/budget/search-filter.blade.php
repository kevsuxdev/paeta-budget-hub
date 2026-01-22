@props(['route', 'searchValue' => '', 'statusValue' => ''])

<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
    <form method="GET" action="{{ $route }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input
                type="text"
                name="search"
                id="search"
                value="{{ $searchValue }}"
                placeholder="Search by title or user name"
                class="w-full border border-black/20 rounded-md p-2 text-sm"
            >
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
            <select name="status" id="status" class="w-full border border-black/20 rounded-md p-2 text-sm">
                <option value="">All Statuses</option>
                <option value="pending" {{ $statusValue == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="reviewed" {{ $statusValue == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                <option value="finance_reviewed" {{ $statusValue == 'finance_reviewed' ? 'selected' : '' }}>Finance Reviewed</option>
                <option value="revise" {{ $statusValue == 'revise' ? 'selected' : '' }}>Revise</option>
                <option value="approved" {{ $statusValue == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $statusValue == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90">
                Search
            </button>
        </div>
    </form>
</div>
