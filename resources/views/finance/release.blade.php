@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-white">Release Quarterly Budget</h1>
    <p class="text-white mb-6">Allocate approved budget amounts to departments for the quarter.</p>

    @if(session('success'))
    <x-alert-message type="success" :message="session('success')" />
    @endif
    @if(session('error'))
    <x-alert-message type="error" :message="session('error')" />
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left: Form -->
        <div class="md:col-span-1">
            <div class="bg-orange-brown rounded-lg p-6 h-full">
                <form action="{{ route('finance.allocate') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Department to Allocate <span class="text-red-400">*</span></label>
                            {{-- Add data-department-releases attribute --}}
                            <select id="departmentSelect"
                                name="department_id"
                                required
                                class="w-full px-3 py-2 bg-primary border border-gray-600 text-white rounded-md"
                                data-department-releases='{{ json_encode($departments->pluck("budget_release", "id")) }}'>
                                <option value="">-- Choose department --</option>
                                @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>

                            <p class="mt-2 text-sm text-gray-300">Current Released: <span id="currentRelease">₱0.00</span></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Amount to Release (PHP) <span class="text-red-400">*</span></label>
                            <input name="amount" type="number" step="0.01" min="0" required class="w-full px-3 py-2 bg-primary border border-gray-600 text-white rounded-md" placeholder="Enter amount" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Quarter (optional)</label>
                            <select name="quarter" class="w-full px-3 py-2 bg-primary border border-gray-600 text-white rounded-md">
                                <option value="">-- Choose Quarter --</option>
                                <option value="Q1">Q1</option>
                                <option value="Q2">Q2</option>
                                <option value="Q3">Q3</option>
                                <option value="Q4">Q4</option>
                            </select>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md">Release & Allocate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Logs -->
        <div class="md:col-span-2">
            <div class="bg-orange-brown rounded-lg p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Recent Release Logs</h2>
                @if(isset($releaseLogs) && $releaseLogs->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-primary">
                        <thead class="bg-orange-brown">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase">User</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase">Department</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-orange-brown divide-y divide-primary">
                            @foreach($releaseLogs as $log)
                            <tr class="hover:bg-primary/50">
                                <td class="px-4 py-2 text-sm text-white">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                <td class="px-4 py-2 text-sm text-white">{{ $log->user->full_name ?? 'System' }}</td>
                                <td class="px-4 py-2 text-sm text-white">{{ $log->user->department->name ?? ($log->budget->department->name ?? '—') }}</td>
                                <td class="px-4 py-2 text-sm text-white">{{ $log->notes }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-300">No release logs yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- JS without @json --}}
<script>
    const deptSelect = document.getElementById('departmentSelect');
    const departmentReleases = JSON.parse(deptSelect.dataset.departmentReleases);

    function formatCurrency(num) {
        return '₱' + Number(num).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    if (deptSelect) {
        deptSelect.addEventListener('change', function(e) {
            const id = e.target.value;
            const val = departmentReleases[id] ?? 0;
            document.getElementById('currentRelease').textContent = formatCurrency(val);
        });
    }
</script>

@endsection