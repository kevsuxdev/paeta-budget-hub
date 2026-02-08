@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <article class="space-y-2">
        <h1 class="text-3xl font-bold text-black">User Management</h1>
        <p class="text-black font-medium">Manage users and view system statistics.</p>
    </article>

    <!-- Department Management Buttons -->
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
    <div class="shadow-sm mb-6">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-orange-brown divide-y divide-primary">
                            @forelse($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $user->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ ucfirst($user->role) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $user->department->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap flex items-center gap-2">
                                    <span @class([ 'inline-flex px-2 py-1 text-xs font-semibold rounded-full' , 'bg-green-100 text-green-800'=> $user->status === 'active',
                                        'bg-red-100 text-red-800' => $user->status !== 'active',
                                        ])>
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button
                                        type="button"
                                        class="ml-2 px-2 py-2 bg-primary cursor-pointer text-white rounded hover:bg-primary/80 text-xs"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->full_name }}"
                                        onclick="openChangePasswordModalFromButton(this)">
                                        Change Password
                                    </button>

                                    <button
                                        type="button"
                                        class="ml-2 px-2 py-2 bg-blue-600 cursor-pointer text-white rounded hover:bg-blue-700 text-xs update-dept-btn"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->full_name }}"
                                        data-department-id="{{ $user->department->id ?? '' }}"
                                        onclick="openUpdateDepartmentModalFromButton(this)">
                                        Update Department
                                    </button>
                                    <button
                                        type="button"
                                        class="ml-2 px-2 py-2 bg-yellow-600 cursor-pointer text-white rounded hover:bg-yellow-700 text-xs edit-user-btn"
                                        data-user-id="{{ $user->id }}"
                                        data-username="{{ $user->username }}"
                                        data-user-name="{{ $user->full_name }}"
                                        data-email="{{ $user->email }}"
                                        data-phone="{{ $user->phone }}"
                                        data-role="{{ $user->role }}"
                                        data-department-id="{{ $user->department->id ?? '' }}"
                                        data-status="{{ $user->status }}"
                                        onclick="openEditUserModalFromButton(this)">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-white">No users found</td>
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
                <!-- Edit User Modal -->
                <div id="editUserModal" class="fixed inset-0 bg-transparent backdrop-blur-sm flex items-center justify-center hidden">
                    <div class="bg-orange-brown p-6 border border-white/60 w-full max-w-lg shadow-lg rounded-md max-h-[90vh] overflow-y-auto">
                        <h3 class="text-xl font-semibold text-white mb-2">Edit User</h3>
                        <form id="editUserForm" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="edit_username" class="block text-sm font-medium text-white mb-1">Username</label>
                                    <input type="text" name="username" id="edit_username" class="w-full border text-white border-white/60 rounded-md p-2 text-sm" required>
                                </div>
                                <div>
                                    <label for="edit_full_name" class="block text-sm font-medium text-white mb-1">Full Name</label>
                                    <input type="text" name="full_name" id="edit_full_name" class="w-full border text-white border-white/60 rounded-md p-2 text-sm" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="edit_email" class="block text-sm font-medium text-white mb-1">Email</label>
                                    <input type="email" id="edit_email" class="w-full border text-white border-white/60 rounded-md p-2 text-sm" required>
                                </div>
                                <div>
                                    <label for="edit_phone" class="block text-sm font-medium text-white mb-1">Phone</label>
                                    <input type="number" name="phone" id="edit_phone" class="w-full border text-white border-white/60 rounded-md p-2 text-sm">
                                </div>
                                <div>
                                    <label for="edit_role" class="block text-sm font-medium text-white mb-1">Role</label>
                                    <select name="role" id="edit_role" class="w-full border text-white border-white/60 rounded-md p-2 text-sm" required>
                                        <option class="text-primary" value="">Select Role</option>
                                        <option class="text-primary" value="admin">Admin</option>
                                        <option class="text-primary" value="finance">Finance</option>
                                        <option class="text-primary" value="staff">Staff</option>
                                        <option class="text-primary" value="dept_head">Department Head</option>
                                    </select>
                                </div>
                                <div id="edit_department_field">
                                    <label for="edit_department_id" class="block text-sm font-medium text-white mb-1">Department</label>
                                    <select name="department_id" id="edit_department_id" class="w-full border text-white border-white/60 rounded-md p-2 text-sm">
                                        <option class="text-primary" value="">Select Department</option>
                                        @foreach($departments as $department)
                                        <option class="text-primary" value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_status" class="block text-sm font-medium text-white mb-1">Status</label>
                                    <select name="status" id="edit_status" class="w-full border text-white border-white/60 rounded-md p-2 text-sm" required>
                                        <option class="text-primary" value="active">Active</option>
                                        <option class="text-primary" value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-end space-x-3 pt-4">
                                <button type="button" onclick="closeEditUserModal()" class="px-4 py-2 bg-red-600 rounded-md hover:bg-red-700 text-white cursor-pointer text-sm">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-primary text-white rounded hover:bg-primary/80 text-sm">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-primary">
                        <tbody class="bg-orange-brown divide-y divide-primary">
                            @forelse($departments as $department)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white flex items-center justify-between">
                                    <span>{{ $department->name }}</span>
                                    <button type="button" class="ml-2 px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 delete-dept-btn" data-department-id="{{ $department->id }}" data-department-name="{{ $department->name }}">Delete</button>
                                </td>
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
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm">
        <h3 class="text-lg font-semibold mb-2 text-gray-800">Change Password for <span id="changePasswordUserName"></span></h3>
        <form id="changePasswordForm" method="POST" action="{{ route('admin.users.changePassword') }}">
            @csrf
            <input type="hidden" name="user_id" id="changePasswordUserId">
            <div class="mb-4">
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" name="new_password" id="new_password" class="w-full border border-gray-300 rounded-md p-2" required minlength="6">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeChangePasswordModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded hover:bg-primary/80 text-sm">Change Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Department Modal -->
<div id="departmentModal" class="fixed inset-0 bg-transparent backdrop-blur-sm flex items-center justify-center hidden">
    <div class="bg-orange-brown p-5 border border-white/60 w-96 shadow-lg rounded-md">
        <h3 class="text-lg font-medium text-white">Add New Department</h3>
        <form action="{{ route('admin.departments.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-white mb-2">Department Name</label>
                <input type="text" name="name" id="name" class="w-full border border-white/60 rounded-md p-2 text-sm text-white" required>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeDepartmentModal()" class="px-4 py-2 bg-red-600 rounded-md hover:bg-red-700 text-sm text-white cursor-pointer">Cancel</button>
                <x-button>Add Department</x-button>
            </div>
        </form>
    </div>
</div>

<!-- User Modal -->
<div id="userModal" class="fixed inset-0 bg-transparent backdrop-blur-sm flex items-center justify-center hidden">
    <div class="bg-orange-brown p-6 border border-white/60 w-full max-w-lg shadow-lg rounded-md max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-semibold text-white mb-2">Add New User</h3>
        <p class="text-sm text-white mb-4">Create a new user account with appropriate permissions.</p>
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-white mb-1">Username</label>
                    <input type="text" name="username" id="username" class="w-full border text-white border-white/60 rounded-md p-2 text-sm" required>
                </div>
                <div>
                    <label for="full_name" class="block text-sm font-medium text-white mb-1">Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="w-full border text-white border-white/60 rounded-md p-2 text-sm" required>
                </div>
                <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-medium text-white mb-1">Email</label>
                    <input type="email" name="email" id="email" class="w-full border text-white border-white/60 rounded-md p-2 text-sm" required>
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-white mb-1">Phone</label>
                    <input type="number" name="phone" id="phone" class="w-full border text-white border-white/60 rounded-md p-2 text-sm">
                </div>
                <input type="hidden" name="password" value="password">
                <input type="hidden" name="password_confirmation" value="password">
                <div>
                    <label for="role" class="block text-sm font-medium text-white mb-1">Role</label>
                    <select name="role" id="role" class="w-full border text-white border-white/60 rounded-md p-2 text-sm" required>
                        <option class="text-primary" value="">Select Role</option>
                        <option class="text-primary" value="admin">Admin</option>
                        <option class="text-primary" value="finance">Finance</option>
                        <option class="text-primary" value="staff">Staff</option>
                        <option class="text-primary" value="dept_head">Department Head</option>
                    </select>
                </div>
                <div id="department-field">
                    <label for="department_id" class="block text-sm font-medium text-white mb-1">Department <span class="text-red-500">*</span></label>
                    <select name="department_id" id="department_id" class="w-full border text-white border-white/60 rounded-md p-2 text-sm">
                        <option class="text-primary" value="">Select Department</option>
                        @foreach($departments as $department)
                        <option class="text-primary" value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-white mb-1">Status</label>
                    <select name="status" id="status" class="w-full border text-white border-white/60 rounded-md p-2 text-sm" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeUserModal()" class="px-4 py-2 bg-red-600 rounded-md hover:bg-red-700 text-white cursor-pointer text-sm">Cancel</button>
                <x-button>Create User</x-button>
            </div>
        </form>
    </div>
</div>

<!-- Update Department Modal -->
<div id="updateDepartmentModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm">
        <h3 class="text-lg font-semibold mb-2 text-gray-800">Update Department for <span id="updateDeptUserName"></span></h3>
        <form id="updateDepartmentForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="update_department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select name="department_id" id="update_department_id" class="w-full border border-gray-300 rounded-md p-2" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeUpdateDepartmentModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded hover:bg-primary/80 text-sm">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Department Modal -->
<div id="deleteDepartmentModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm">
        <h3 class="text-lg font-semibold mb-2 text-gray-800">Delete Department</h3>
        <form id="deleteDepartmentForm" method="POST">
            @csrf
            @method('DELETE')
            <p class="mb-4 text-gray-700">Are you sure you want to delete the department <strong id="deleteDeptName"></strong>? This action cannot be undone.</p>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeDeleteDepartmentModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">Delete</button>
            </div>
        </form>
    </div>
</div>

<!-- JS Section -->
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Base URL for update department form
        window.updateDepartmentFormBase = "{{ url('admin/users') }}";

        // Department Modal
        window.openDepartmentModal = function() {
            document.getElementById('departmentModal').classList.remove('hidden');
        };
        window.closeDepartmentModal = function() {
            document.getElementById('departmentModal').classList.add('hidden');
        };

        // User Modal
        window.openUserModal = function() {
            document.getElementById('userModal').classList.remove('hidden');
        };
        window.closeUserModal = function() {
            document.getElementById('userModal').classList.add('hidden');
        };

        // Change Password Modal
        window.openChangePasswordModalFromButton = function(button) {
            const userId = button.dataset.userId;
            const userName = button.dataset.userName;
            window.openChangePasswordModal(userId, userName);
        };
        window.openChangePasswordModal = function(userId, userName) {
            const modal = document.getElementById('changePasswordModal');
            const form = document.getElementById('changePasswordForm');
            const nameSpan = document.getElementById('changePasswordUserName');
            if (!modal || !form) return;
            form.user_id.value = userId;
            nameSpan.textContent = userName;
            modal.classList.remove('hidden');
        };
        window.closeChangePasswordModal = function() {
            const modal = document.getElementById('changePasswordModal');
            const form = document.getElementById('changePasswordForm');
            if (!modal) return;
            modal.classList.add('hidden');
            form.reset();
        };

        // Update Department Modal
        window.openUpdateDepartmentModalFromButton = function(button) {
            const userId = button.dataset.userId;
            const userName = button.dataset.userName;
            const deptId = button.dataset.departmentId || '';
            window.openUpdateDepartmentModal(userId, deptId, userName);
        };
        window.openUpdateDepartmentModal = function(userId, deptId, userName) {
            const modal = document.getElementById('updateDepartmentModal');
            const form = document.getElementById('updateDepartmentForm');
            const nameSpan = document.getElementById('updateDeptUserName');
            const select = document.getElementById('update_department_id');
            if (!modal || !form) return;
            nameSpan.textContent = userName;
            select.value = deptId;
            form.action = `${window.updateDepartmentFormBase}/${userId}`;
            modal.classList.remove('hidden');
        };
        window.closeUpdateDepartmentModal = function() {
            const modal = document.getElementById('updateDepartmentModal');
            const form = document.getElementById('updateDepartmentForm');
            if (!modal) return;
            modal.classList.add('hidden');
            form.reset();
        };

        // Edit User Modal
        window.openEditUserModalFromButton = function(button) {
            const userId = button.dataset.userId;
            const username = button.dataset.username || '';
            const userName = button.dataset.userName || '';
            const email = button.dataset.email || '';
            const phone = button.dataset.phone || '';
            const role = button.dataset.role || '';
            const deptId = button.dataset.departmentId || '';
            const status = button.dataset.status || 'active';
            window.openEditUserModal(userId, {
                username,
                userName,
                email,
                phone,
                role,
                deptId,
                status
            });
        };

        window.openEditUserModal = function(userId, data) {
            const modal = document.getElementById('editUserModal');
            const form = document.getElementById('editUserForm');
            if (!modal || !form) return;
            // Populate fields
            form.action = `${window.updateDepartmentFormBase}/${userId}`;
            document.getElementById('edit_username').value = data.username || '';
            document.getElementById('edit_full_name').value = data.userName || '';
            document.getElementById('edit_email').value = data.email || '';
            document.getElementById('edit_phone').value = data.phone || '';
            document.getElementById('edit_role').value = data.role || '';
            document.getElementById('edit_department_id').value = data.deptId || '';
            document.getElementById('edit_status').value = data.status || 'active';
            // Show/hide department field based on role
            const deptField = document.getElementById('edit_department_field');
            const deptSelect = document.getElementById('edit_department_id');
            if (data.role === 'staff' || data.role === 'dept_head') {
                deptField.style.display = 'block';
                deptSelect.required = true;
            } else {
                deptField.style.display = 'none';
                deptSelect.required = false;
            }

            modal.classList.remove('hidden');
        };

        window.closeEditUserModal = function() {
            const modal = document.getElementById('editUserModal');
            const form = document.getElementById('editUserForm');
            if (!modal) return;
            modal.classList.add('hidden');
            if (form) form.reset();
        };

        // Handle role change inside edit modal
        const editRoleEl = document.getElementById('edit_role');
        if (editRoleEl) {
            editRoleEl.addEventListener('change', function() {
                const departmentField = document.getElementById('edit_department_field');
                const departmentSelect = document.getElementById('edit_department_id');
                const role = this.value;

                if (role === 'staff' || role === 'dept_head') {
                    departmentField.style.display = 'block';
                    departmentSelect.required = true;
                } else {
                    departmentField.style.display = 'none';
                    departmentSelect.required = false;
                    departmentSelect.value = '';
                }
            });
        }

        // Delete Department Modal
        window.deleteDepartmentFormBase = "{{ url('admin/departments') }}";
        window.openDeleteDepartmentModalFromButton = function(button) {
            const deptId = button.dataset.departmentId;
            const deptName = button.dataset.departmentName || '';
            window.openDeleteDepartmentModal(deptId, deptName);
        };
        window.openDeleteDepartmentModal = function(deptId, deptName) {
            const modal = document.getElementById('deleteDepartmentModal');
            const form = document.getElementById('deleteDepartmentForm');
            const nameSpan = document.getElementById('deleteDeptName');
            if (!modal || !form) return;
            nameSpan.textContent = deptName;
            form.action = `${window.deleteDepartmentFormBase}/${deptId}`;
            modal.classList.remove('hidden');
        };
        window.closeDeleteDepartmentModal = function() {
            const modal = document.getElementById('deleteDepartmentModal');
            const form = document.getElementById('deleteDepartmentForm');
            if (!modal) return;
            modal.classList.add('hidden');
            if (form) form.reset();
        };

        // Close modals on outside click
        window.addEventListener('click', function(event) {
            ['departmentModal', 'userModal', 'updateDepartmentModal', 'changePasswordModal', 'deleteDepartmentModal'].forEach(id => {
                const modal = document.getElementById(id);
                if (modal && event.target === modal) modal.classList.add('hidden');
            });
        });

        // Delegated click handler for delete buttons
        document.addEventListener('click', function(e) {
            const btn = e.target.closest && e.target.closest('.delete-dept-btn');
            if (!btn) return;
            if (typeof window.openDeleteDepartmentModalFromButton === 'function') {
                window.openDeleteDepartmentModalFromButton(btn);
            }
        });

        // Handle department field visibility based on role in User Modal
        const roleEl = document.getElementById('role');
        if (roleEl) {
            roleEl.addEventListener('change', function() {
                const departmentField = document.getElementById('department-field');
                const departmentSelect = document.getElementById('department_id');
                const role = this.value;

                if (role === 'staff' || role === 'dept_head') {
                    departmentField.style.display = 'block';
                    departmentSelect.required = true;
                } else {
                    departmentField.style.display = 'none';
                    departmentSelect.required = false;
                    departmentSelect.value = '';
                }
            });
            // Trigger change to set initial visibility
            roleEl.dispatchEvent(new Event('change'));
        }

    });
</script>
@endsection