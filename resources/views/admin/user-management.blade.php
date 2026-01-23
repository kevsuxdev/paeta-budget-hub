@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <article class="space-y-2">
        <h1 class="text-2xl font-bold text-white">User Management</h1>
        <p class="text-white">Manage users and view system statistics.</p>
    </article>
    <!-- Department Management -->
    <div class="flex items-center justify-end">
        <div class="space-x-2 my-5 self-end">
            <button type="button" onclick="openDepartmentModal()" class="bg-primary text-sm text-white px-4 py-2 rounded-md hover:bg-opacity-90">
                Add New Department
            </button>
            <button type="button" onclick="openUserModal()" class="bg-green-600 text-sm text-white px-4 py-2 rounded-md hover:bg-green-700">
                Add New User
            </button>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="shadow-sm">
        <h2 class="text-2xl font-semibold text-white mb-4">User Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Total Users -->
            <div class="flex items-center bg-orange-brown p-6 rounded-lg ">

                <div class="ml-3">
                    <p class="text-sm text-white">Total Users</p>
                    <p class="text-2xl font-bold text-blue-500">{{ $totalUsers }}</p>
                </div>
            </div>

            <!-- Total Active Users -->
            <div class="flex items-center bg-orange-brown p-6 rounded-lg ">
                <div class="ml-3">
                    <p class="text-sm text-white">Active Users</p>
                    <p class="text-2xl font-bold text-green-500">{{ $totalActiveUsers }}</p>
                </div>
            </div>

            <!-- Total Departments -->
            <div class="flex items-center bg-orange-brown p-6 rounded-lg ">
                <div class="ml-3">
                    <p class="text-sm text-white">Total Departments</p>
                    <p class="text-2xl font-bold text-purple-500">{{ $totalDepartments }}</p>
                </div>
            </div>
        </div>
    </div>


    <!-- Users and Departments Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-6">
        <!-- Users List -->
        <div class="lg:col-span-3">
            <div class="bg-orange-brown rounded-lg shadow-sm">
                <div class="px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">List of Users</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-primary">
                        <thead class="bg-orange-brown">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-orange-brown divide-y divide-primary">
                            @forelse($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $user->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ ucfirst($user->role) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $user->department->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span @class([ 'inline-flex px-2 py-1 text-xs font-semibold rounded-full' , 'bg-green-100 text-green-800'=> $user->status === 'active',
                                        'bg-red-100 text-red-800' => $user->status !== 'active',
                                        ])>
                                        {{ ucfirst($user->status) }}
                                    </span>

                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-white">No users found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Departments List -->
        <div>
            <div class="bg-orange-brown rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-primary">
                    <h3 class="text-lg font-semibold text-white">Departments</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-primary">
                        <thead class="bg-orange-brown">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Department Name</th>
                            </tr>
                        </thead>
                        <tbody class="bg-orange-brown divide-y divide-primary">
                            @forelse($departments as $department)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $department->name }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td class="px-6 py-4 text-center text-sm text-white">No departments found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Modal -->
    <div id="departmentModal" class="fixed inset-0 bg-transparent backdrop-blur-sm flex items-center justify-center hidden">
        <div class="bg-orange-brown p-5 border border-black/20 w-96 shadow-lg rounded-md">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Department</h3>
                <form action="{{ route('admin.departments.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Department Name</label>
                        <input type="text" name="name" id="name" class="w-full border border-black/20 rounded-md p-2 text-sm" required>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeDepartmentModal()" class="px-4 py-2 bg-gray-300 text-gray-900 rounded-md hover:bg-gray-400">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-opacity-90">Add Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Modal -->
    <div id="userModal" class="fixed inset-0 bg-transparent backdrop-blur-sm flex items-center justify-center hidden">
        <div class="bg-orange-brown p-6 border border-black/20 w-full max-w-lg shadow-lg rounded-md max-h-[90vh] overflow-y-auto">
            <div class="mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Add New User</h3>
                <p class="text-sm text-white">Create a new user account with appropriate permissions.</p>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" id="username" class="w-full border border-black/20 rounded-md p-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="full_name" id="full_name" class="w-full border border-black/20 rounded-md p-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" class="w-full border border-black/20 rounded-md p-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" name="phone" id="phone" class="w-full border border-black/20 rounded-md p-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <input type="hidden" name="password" value="password">
                    <input type="hidden" name="password_confirmation" value="password">
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" id="role" class="w-full border border-black/20 rounded-md p-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="finance">Finance</option>
                            <option value="staff">Staff</option>
                            <option value="dept_head">Department Head</option>
                        </select>
                    </div>
                    <div id="department-field">
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department <span class="text-red-500">*</span></label>
                        <select name="department_id" id="department_id" class="w-full border border-black/20 rounded-md p-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="w-full border border-black/20 rounded-md p-2 text-sm focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </input>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeUserModal()" class="px-4 py-2 bg-gray-300 text-gray-900 rounded-md hover:bg-gray-400 transition-colors text-sm">Cancel</button>
                    <x-button>Create User</x-button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 mt-6">
        {{ session('success') }}
    </div>
    @endif
</div>

<script>
    function openDepartmentModal() {
        document.getElementById('departmentModal').classList.remove('hidden');
    }

    function closeDepartmentModal() {
        document.getElementById('departmentModal').classList.add('hidden');
    }

    function openUserModal() {
        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    // Handle department field visibility based on role
    document.getElementById('role').addEventListener('change', function() {
        const departmentField = document.getElementById('department-field');
        const departmentSelect = document.getElementById('department_id');
        const role = this.value;

        // Show department field for staff and dept_head, hide for admin and finance
        if (role === 'staff' || role === 'dept_head') {
            departmentField.style.display = 'block';
            departmentSelect.required = true;
        } else {
            departmentField.style.display = 'none';
            departmentSelect.required = false;
            departmentSelect.value = ''; // Clear selection
        }
    });

    // Close modals when clicking outside
    window.onclick = function(event) {
        const deptModal = document.getElementById('departmentModal');
        const userModal = document.getElementById('userModal');
        if (event.target == deptModal) {
            deptModal.classList.add('hidden');
        }
        if (event.target == userModal) {
            userModal.classList.add('hidden');
        }
    }
</script>
@endsection