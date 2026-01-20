<aside class="w-64 bg-primary text-background min-h-screen p-4">
    <h1 class="text-xl font-bold mb-6">Paeta Budget Hub</h1>
    <nav class="space-y-2">
        @auth
            @if(auth()->user()->role === 'admin')
                <x-nav-links href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-links>
                <x-nav-links href="{{ route('admin.budget.create') }}" :active="request()->routeIs('admin.budget.create')">Budget Submission</x-nav-links>
                <x-nav-links href="{{ route('admin.document.tracking') }}" :active="request()->routeIs('admin.document.tracking')">Document Tracking</x-nav-links>
                <x-nav-links href="{{ route('admin.finance.review') }}" :active="request()->routeIs('admin.finance.review')">Finance Review</x-nav-links>
                <x-nav-links>Budget Approval</x-nav-links>
                <x-nav-links href="{{ route('admin.user.management') }}" :active="request()->routeIs('admin.user.management')">User Management</x-nav-links>
                <x-nav-links>Audit Trail</x-nav-links>
                <x-nav-links>Archive</x-nav-links>
            @elseif(auth()->user()->role === 'staff')
                <x-nav-links href="{{ route('staff.dashboard') }}" :active="request()->routeIs('staff.dashboard')">Dashboard</x-nav-links>
                <x-nav-links>Budget Submission</x-nav-links>
                <x-nav-links>Document Tracking</x-nav-links>
            @elseif(auth()->user()->role === 'dept_head')
                <x-nav-links href="{{ route('dept_head.dashboard') }}" :active="request()->routeIs('dept_head.dashboard')">Dashboard</x-nav-links>
                <x-nav-links>Budget Submission</x-nav-links>
                <x-nav-links>Document Tracking</x-nav-links>
                <x-nav-links>Budget Approval</x-nav-links>
            @elseif(auth()->user()->role === 'finance')
                <x-nav-links href="{{ route('finance.dashboard') }}" :active="request()->routeIs('finance.dashboard')">Dashboard</x-nav-links>
                <x-nav-links href="{{ route('finance.review') }}" :active="request()->routeIs('finance.review')">Finance Review</x-nav-links>
                <x-nav-links>Budget Approval</x-nav-links>
                <x-nav-links>Audit Trail</x-nav-links>
            @endif
        @endauth
    </nav>
</aside>