@props(['route', 'searchValue' => '', 'statusValue' => ''])

<div class="bg-orange-brown p-4 rounded-lg shadow-sm border border-primary mb-6">
    <form method="GET" action="{{ $route }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-white mb-1">Search</label>
            <input
                type="text"
                name="search"
                id="search"
                value="{{ $searchValue }}"
                placeholder="Search by title or user name"
                class="w-full border border-black/20 text-white rounded-md p-2 text-sm"
            >
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-white mb-1">Filter by Status</label>
            <select name="status" id="status" class="w-full border border-black/20 text-white rounded-md p-2 text-sm">
                <option class="text-primary" value="">All Statuses</option>
                <option class="text-primary" value="pending" {{ $statusValue == 'pending' ? 'selected' : '' }}>Pending</option>
                <option class="text-primary" value="reviewed" {{ $statusValue == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                <option class="text-primary" value="finance_reviewed" {{ $statusValue == 'finance_reviewed' ? 'selected' : '' }}>Finance Reviewed</option>
                <option class="text-primary" value="revise" {{ $statusValue == 'revise' ? 'selected' : '' }}>Revise</option>
                <option class="text-primary" value="approved" {{ $statusValue == 'approved' ? 'selected' : '' }}>Approved</option>
                <option class="text-primary" value="rejected" {{ $statusValue == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="bg-primary text-sm text-white px-4 py-2 rounded-md hover:bg-opacity-90">
                Search
            </button>
        </div>
    </form>
</div>
