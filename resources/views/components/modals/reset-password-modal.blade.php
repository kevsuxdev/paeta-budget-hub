<div id="resetPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-transparent hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-8 relative">
        <button type="button" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700" onclick="closeResetPasswordModal()">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Reset Your Password</h2>
        <p class="text-gray-600 mb-4">For your security, please reset your password before continuing to use the system.</p>
        <form id="resetPasswordForm" method="POST" action="{{ route('user.resetPassword') }}">
            @csrf
            <div class="mb-4">
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password <span class="text-red-500">*</span></label>
                <input type="password" id="new_password" name="new_password" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring focus:ring-orange-200">
            </div>
            <div class="mb-4">
                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring focus:ring-orange-200">
            </div>
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 rounded-md transition">Reset Password</button>
        </form>
    </div>
</div>
<script>
function openResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.remove('hidden');
}
function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
}
// Password match validation
const form = document.getElementById('resetPasswordForm');
if(form) {
    form.addEventListener('submit', function(e) {
        const pw = document.getElementById('new_password').value;
        const cpw = document.getElementById('confirm_password').value;
        if(pw !== cpw) {
            e.preventDefault();
            alert('Passwords do not match.');
        }
    });
}
</script>
