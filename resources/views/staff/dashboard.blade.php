@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-primary mb-4">Staff Dashboard</h1>
    <p class="text-gray-600 mb-2">Welcome, {{ $user->full_name }}.</p>
    <p class="text-gray-600 mb-6">Department: <span class="text-primary font-medium">{{ $user->department->name }}</span> | Access your budget submissions and tracking.</p>

    <!-- Department Statistics -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Department Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Total Department Budgets -->
            <div class="flex items-center">
                <svg class="w-10 h-10 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Total Budgets</p>
                    <p class="text-2xl font-bold text-blue-500">{{ $totalDeptBudgets }}</p>
                </div>
            </div>

            <!-- Pending Department Budgets -->
            <div class="flex items-center">
                <svg class="w-10 h-10 text-yellow-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-500">{{ $pendingDeptBudgets }}</p>
                </div>
            </div>

            <!-- Approved Department Budgets -->
            <div class="flex items-center">
                <svg class="w-10 h-10 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Approved</p>
                    <p class="text-2xl font-bold text-green-500">{{ $approvedDeptBudgets }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection