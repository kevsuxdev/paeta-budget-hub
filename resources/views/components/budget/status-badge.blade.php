@props(['status'])

@php
    $classes = match($status) {
        'pending' => 'bg-yellow-100 text-yellow-800',
        'reviewed' => 'bg-blue-100 text-blue-800',
        'finance_reviewed' => 'bg-purple-100 text-purple-800',
        'revise' => 'bg-orange-100 text-orange-800',
        'approved' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };

    $label = match($status) {
        'finance_reviewed' => 'Finance Reviewed',
        default => ucfirst($status),
    };
@endphp

<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $classes }}">
    {{ $label }}
</span>
