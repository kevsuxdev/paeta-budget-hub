@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-black mb-1">Edit Budget Request</h1>
        <p class="text-black font-medium">Update the budget request details and supporting documents.</p>
    </div>

    <form action="{{ route('dept_head.budget.update', $budget) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- First Section: Budget Details -->
        <div class="bg-orange-brown p-6 rounded-lg shadow-sm border border-primary text-white">
            <h2 class="text-2xl font-semibold text-white mb-4">Budget Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    @php $user = request()->user(); @endphp
                    <label for="department_id" class="block text-sm font-medium text-white mb-2">Department</label>
                    <select id="department_id" class="w-full border border-white/60 rounded-md text-white p-2 text-sm bg-transparent" disabled>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ $department->id == ($budget->department_id ?? null) ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="department_id" value="{{ $budget->department_id ?? '' }}">
                </div>

                <x-input-fields name="title" label="Title" type="text" :value="$budget->title" />
                <x-input-fields name="fiscal_year" label="Fiscal Year" type="number" :value="$budget->fiscal_year" />
                <x-input-fields name="category" label="Category" type="text" :value="$budget->category" />
                <x-input-fields name="submission_date" label="Due Date" type="date" min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" :value="$budget->submission_date->format('Y-m-d')" />
            </div>
            <div class="mt-6">
                <label for="justification" class="block text-sm font-medium text-white mb-2">Justification</label>
                <textarea name="justification" id="justification" rows="4" class="w-full border border-white/60 bg-transparent rounded-md p-2 text-sm text-white">{{ $budget->justification }}</textarea>
            </div>
        </div>

        <!-- Second Section: Budget Line Items -->
        <div class="bg-orange-brown w-full p-6 rounded-lg shadow-sm border border-primary text-white">
            <h2 class="text-xl font-semibold text-white mb-6">Budget Line Items</h2>
            <div id="line-items" class="space-y-4 w-full">
                @foreach($budget->lineItems as $index => $item)
                <div class="line-item grid grid-cols-1 md:grid-cols-5 gap-4 items-end w-full">
                    <x-input-fields name="line_items[{{ $index }}][description]" label="Description" type="text" :value="$item->description" />
                    <article class="w-full">
                        <label class="text-sm font-medium">Quantity</label>
                        <input type="number" name="line_items[{{ $index }}][quantity]" min="1" class="w-full bg-transparent border border-white/60 rounded-md p-2 text-sm quantity text-white" value="{{ $item->quantity }}" required>
                    </article>
                    <article class="w-full">
                        <label class="text-sm font-medium">Unit Cost</label>
                        <input type="number" step="0.01" name="line_items[{{ $index }}][unit_cost]" min="0" class="w-full bg-transparent border border-white/60 rounded-md p-2 text-sm unit-cost text-white" value="{{ $item->unit_cost }}" required>
                    </article>
                    <article class="w-full">
                        <label class="text-sm font-medium">Total Cost</label>
                        <input type="number" step="0.01" class="w-full bg-transparent border border-white/60 rounded-md p-2 text-sm total-cost text-white" readonly value="{{ $item->total_cost }}">
                    </article>
                    <div class="flex items-end">
                        <button type="button" class="remove-item bg-red-600 text-white px-3 py-2 rounded-md hover:bg-red-700">Remove</button>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 flex justify-between items-center">
                <button type="button" id="add-item" class="bg-primary text-white px-4 text-sm py-2 rounded-md hover:bg-opacity-90">Add Line Item</button>
                <div class="text-lg font-semibold text-white">
                    Grand Total: <span id="grand-total" class="text-sm text-primary">{{ number_format($budget->total_budget, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Third Section: Supporting Documents -->
        <div class="bg-orange-brown p-6 rounded-lg shadow-sm border border-primary text-white">
            <h2 class="text-xl font-semibold text-white mb-6">Supporting Documents</h2>

            <p class="text-sm text-white/80 mb-2">Current file:
                @if($budget->supporting_document)
                    <a href="{{ asset('storage/' . $budget->supporting_document) }}" target="_blank" class="underline text-blue-200">View</a>
                @else
                    None
                @endif
            </p>

            <label
                for="supporting_document"
                id="dropzone"
                class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-white/40 text-white rounded-lg cursor-pointer bg-transparent hover:bg-white/5 transition text-center">
                <svg class="w-8 h-8 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16V12M7 12V8M7 12h4m6 4v-1a3 3 0 00-3-3H6a3 3 0 00-3 3v1" />
                </svg>

                <p class="text-sm text-white/80" id="upload-text">
                    <span class="font-medium text-white">Click to upload</span> or drag and drop
                </p>
                <p class="text-xs text-white mt-1">
                    PDF, DOC, DOCX, JPG, JPEG, PNG
                </p>

                <input
                    id="supporting_document"
                    name="supporting_document"
                    type="file"
                    class="hidden"
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
            </label>
        </div>

        <div class="flex justify-end">
            <x-button>Save Changes</x-button>
        </div>
    </form>
</div>

<script>
    let itemCount = {{ max(1, $budget->lineItems->count()) }};

    function calculateTotal(item) {
        const quantity = parseFloat(item.querySelector('.quantity').value) || 0;
        const unitCost = parseFloat(item.querySelector('.unit-cost').value) || 0;
        const total = quantity * unitCost;
        item.querySelector('.total-cost').value = total.toFixed(2);
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.total-cost').forEach(input => {
            grandTotal += parseFloat(input.value) || 0;
        });
        document.getElementById('grand-total').textContent = grandTotal.toFixed(2);
    }

    document.getElementById('add-item').addEventListener('click', function() {
        const lineItems = document.getElementById('line-items');
        const newItem = document.createElement('div');
        newItem.className = 'line-item grid grid-cols-1 md:grid-cols-5 gap-4 items-end';
        newItem.innerHTML = `
            <article>
                <label class="text-sm font-medium">Description</label>
                <input type="text" name="line_items[${itemCount}][description]" class="w-full border border-black/20 rounded-md p-2 text-sm" required>
            </article>
            <article>
                <label class="text-sm font-medium">Quantity</label>
                <input type="number" name="line_items[${itemCount}][quantity]" min="1" class="w-full border border-black/20 rounded-md p-2 text-sm quantity" required>
            </article>
            <article>
                <label class="text-sm font-medium">Unit Cost</label>
                <input type="number" step="0.01" name="line_items[${itemCount}][unit_cost]" min="0" class="w-full border border-black/20 rounded-md p-2 text-sm unit-cost" required>
            </article>
            <article>
                <label class="text-sm font-medium">Total Cost</label>
                <input type="number" step="0.01" class="w-full border border-black/20 rounded-md p-2 text-sm total-cost" readonly>
            </article>
            <div class="flex items-end">
                <button type="button" class="remove-item bg-red-500 text-primary px-3 py-2 rounded-md hover:bg-red-600">Remove</button>
            </div>
        `;
        lineItems.appendChild(newItem);
        itemCount++;
        updateRemoveButtons();
        attachEventListeners(newItem);
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.line-item').remove();
            updateRemoveButtons();
            calculateGrandTotal();
        }
    });

    function updateRemoveButtons() {
        const items = document.querySelectorAll('.line-item');
        items.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-item');
            if (items.length > 1) {
                removeBtn.classList.remove('hidden');
            } else {
                removeBtn.classList.add('hidden');
            }
        });
    }

    function attachEventListeners(item) {
        const qty = item.querySelector('.quantity');
        const unit = item.querySelector('.unit-cost');
        if (qty) qty.addEventListener('input', () => calculateTotal(item));
        if (unit) unit.addEventListener('input', () => calculateTotal(item));
    }

    // Attach listeners to initial items
    document.querySelectorAll('.line-item').forEach(item => {
        attachEventListeners(item);
    });

    // Initial calculation
    calculateGrandTotal();

    // File upload functionality
    const input = document.getElementById('supporting_document');
    const uploadText = document.getElementById('upload-text');
    const dropzone = document.getElementById('dropzone');

    input.addEventListener('change', () => {
        if (input.files.length) {
            uploadText.innerHTML = `<span class="font-medium text-green-600">${input.files[0].name}</span> selected`;
        } else {
            uploadText.innerHTML = `<span class="font-medium text-primary">Click to upload</span> or drag and drop`;
        }
    });

    // Drag styling
    ['dragenter', 'dragover'].forEach(event => {
        dropzone.addEventListener(event, e => {
            e.preventDefault();
            dropzone.classList.add('border-primary', 'bg-primary/5');
        });
    });

    ['dragleave', 'drop'].forEach(event => {
        dropzone.addEventListener(event, e => {
            e.preventDefault();
            dropzone.classList.remove('border-primary', 'bg-primary/5');
        });
    });
</script>
@endsection
