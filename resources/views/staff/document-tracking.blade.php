@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-primary mb-4">Document Tracking</h1>
    <p class="text-gray-600 mb-6">Track budget requests from your department with search and filter capabilities.</p>

    <!-- Search and Filter -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
        <form method="GET" action="{{ route('staff.document.tracking') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by title or user name" class="w-full border border-black/20 rounded-md p-2 text-sm">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                <select name="status" id="status" class="w-full border border-black/20 rounded-md p-2 text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90">Search</button>
            </div>
        </form>
    </div>

    <!-- Budget Requests Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($budgets as $budget)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $budget->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $budget->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $budget->user->full_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $budget->department->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($budget->total_budget, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span @class([ 'inline-flex px-2 py-1 text-xs font-semibold rounded-full' , 'bg-yellow-100 text-yellow-800'=> $budget->status === 'pending',
                                'bg-green-100 text-green-800' => $budget->status === 'approved',
                                'bg-red-100 text-red-800' => $budget->status === 'rejected',
                                ])>
                                {{ ucfirst($budget->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $budget->submission_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <x-button type="button"
                                onclick="openModalFromButton(this)"
                                data-id="{{ $budget->id }}"
                                data-title="{{ $budget->title }}"
                                data-status="{{ $budget->status }}"
                                data-date="{{ optional($budget->submission_date)->format('M d, Y') }}"
                                data-user="{{ optional($budget->user)->full_name ?? 'N/A' }}">
                                View Details
                            </x-button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No budget requests found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($budgets->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $budgets->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection