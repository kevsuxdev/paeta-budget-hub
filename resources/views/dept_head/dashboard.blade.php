@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <article class="space-y-2">
        <h1 class="text-2xl font-bold text-white">Dashboard</h1>
        <p class="text-white">Welcome, Department Head. Review and approve budget submissions.</p>
        <p class="text-sm font-medium text-white bg-accent p-2 rounded-xl w-fit px-4">{{ auth()->user()->department->name ?? 'N/A' }}</p>
    </article>
    <!-- Add dept_head-specific content here -->
</div>
@endsection