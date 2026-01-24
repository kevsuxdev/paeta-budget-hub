<aside class="w-64 bg-primary text-background min-h-screen p-4">
    <h1 class="text-xl font-bold mb-6">Paeta Budget Hub</h1>
    <nav class="space-y-2">
        @auth
            @if(auth()->user()->role === 'admin')
                <x-nav-links href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-links>
                <x-nav-links href="{{ route('admin.budget.create') }}" :active="request()->routeIs('admin.budget.create')">Budget Submission</x-nav-links>
                <x-nav-links href="{{ route('admin.document.tracking') }}" :active="request()->routeIs('admin.document.tracking')">Document Tracking</x-nav-links>
                <x-nav-links href="{{ route('admin.finance.review') }}" :active="request()->routeIs('admin.finance.review')">Finance Review</x-nav-links>
                <x-nav-links href="{{ route('admin.approval') }}" :active="request()->routeIs('admin.approval')">Final Approval</x-nav-links>
                <x-nav-links href="{{ route('admin.user.management') }}" :active="request()->routeIs('admin.user.management')">User Management</x-nav-links>
                <x-nav-links href="{{ route('admin.audit.trail') }}" :active="request()->routeIs('admin.audit.trail')">Audit Trail</x-nav-links>
                <x-nav-links href="{{ route('admin.archive') }}" :active="request()->routeIs('admin.archive')">Archive</x-nav-links>
            @elseif(auth()->user()->role === 'staff')
                <x-nav-links href="{{ route('staff.dashboard') }}" :active="request()->routeIs('staff.dashboard')">Dashboard</x-nav-links>
                <x-nav-links href="{{ route('staff.budget.create') }}" :active="request()->routeIs('staff.budget.create')">Budget Submission</x-nav-links>
                <x-nav-links href="{{ route('staff.document.tracking') }}" :active="request()->routeIs('staff.document.tracking')">Document Tracking</x-nav-links>
            @elseif(auth()->user()->role === 'dept_head')
                <x-nav-links href="{{ route('dept_head.dashboard') }}" :active="request()->routeIs('dept_head.dashboard')">Dashboard</x-nav-links>
                <x-nav-links href="{{ route('dept_head.budget.create') }}" :active="request()->routeIs('dept_head.budget.create')">Budget Submission</x-nav-links>
                <x-nav-links href="{{ route('dept_head.document.tracking') }}" :active="request()->routeIs('dept_head.document.tracking')">Document Tracking</x-nav-links>
            @elseif(auth()->user()->role === 'finance')
                <x-nav-links href="{{ route('finance.dashboard') }}" :active="request()->routeIs('finance.dashboard')">Dashboard</x-nav-links>
                <x-nav-links href="{{ route('finance.review') }}" :active="request()->routeIs('finance.review')">Finance Review</x-nav-links>
                <x-nav-links href="{{ route('finance.approval') }}" :active="request()->routeIs('finance.approval')">Final Approval</x-nav-links>
                <x-nav-links href="{{ route('finance.audit.trail') }}" :active="request()->routeIs('finance.audit.trail')">Audit Trail</x-nav-links>
                <x-nav-links href="{{ route('finance.archive') }}" :active="request()->routeIs('finance.archive')">Archive</x-nav-links>
            @endif
        @endauth
        @auth
            <div class="mt-auto pt-4 border-t border-gray-200">
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-white text-sm hover:bg-primary rounded-md transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        @endauth
    </nav>
</aside>