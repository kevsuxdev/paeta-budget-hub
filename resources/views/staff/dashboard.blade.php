@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6 space-y-5">
    <h1 class="text-2xl font-bold text-white mb-4">Dashboard</h1>
    <article class="space-y-2">
        <p class="text-white text-xl font-medium">Welcome, {{ $user->full_name }}!</p>
        <p class="text-sm font-medium text-white bg-accent p-2 rounded-xl w-fit px-4">{{ auth()->user()->department->name ?? 'N/A' }}</p>
    </article>
    <!-- Department Statistics -->
    <div class="">
        <h2 class="text-xl font-semibold text-white mb-4">Department Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Total Department Budgets -->
            <div class="flex items-center bg-orange-brown p-6 rounded-lg shadow-sm">
                <div>
                    <p class="text-base text-white">Total Budgets</p>
                    <p class="text-2xl font-bold text-blue-500">{{ $totalDeptBudgets }}</p>
                </div>
            </div>

            <!-- Pending Department Budgets -->
            <div class="flex items-center bg-orange-brown p-6 rounded-lg shadow-sm">
                <div>
                    <p class="text-base text-white">Pending</p>
                    <p class="text-2xl font-bold text-yellow-500">{{ $pendingDeptBudgets }}</p>
                </div>
            </div>

            <!-- Approved Department Budgets -->
            <div class="flex items-center bg-orange-brown p-6 rounded-lg shadow-sm">
                <div>
                    <p class="text-base text-white">Approved</p>
                    <p class="text-2xl font-bold text-green-500">{{ $approvedDeptBudgets }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection