@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-primary mb-2">Budget Request Submission</h1>
        <p class="text-gray-600">Submit a new budget request by filling out the details below. Include all necessary line items and supporting documentation.</p>
    </div>

    <form action="{{ route('staff.budget.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- First Section: Budget Details -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Budget Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select name="department_id" id="department_id" class="w-full border border-black/20 rounded-md p-2 text-sm" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ $user->department->id == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <x-input-fields name="title" label="Title" type="text" />
                <x-input-fields name="fiscal_year" label="Fiscal Year" type="text" />
                <x-input-fields name="category" label="Category" type="text" />
                <x-input-fields name="submission_date" label="Submission Date" type="date" />
            </div>
            <div class="mt-6">
                <label for="justification" class="block text-sm font-medium text-gray-700 mb-2">Justification</label>
                <textarea name="justification" id="justification" rows="4" class="w-full border border-black/20 rounded-md p-2 text-sm"></textarea>
            </div>
        </div>

        <!-- Second Section: Budget Line Items -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Budget Line Items</h2>
            <div id="line-items" class="space-y-4">
                <div class="line-item grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <x-input-fields name="line_items[0][description]" label="Description" type="text" />
                    <article>
                        <label class="text-sm font-medium">Quantity</label>
                        <input type="number" name="line_items[0][quantity]" min="1" class="w-full border border-black/20 rounded-md p-2 text-sm quantity" required>
                    </article>
                    <article>
                        <label class="text-sm font-medium">Unit Cost</label>
                        <input type="number" step="0.01" name="line_items[0][unit_cost]" min="0" class="w-full border border-black/20 rounded-md p-2 text-sm unit-cost" required>
                    </article>
                    <article>
                        <label class="text-sm font-medium">Total Cost</label>
                        <input type="number" step="0.01" class="w-full border border-black/20 rounded-md p-2 text-sm total-cost" readonly>
                    </article>
                    <div class="flex items-end">
                        <button type="button" class="remove-item bg-red-500 text-white px-3 py-2 rounded-md hover:bg-red-600 hidden">Remove</button>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex justify-between items-center">
                <button type="button" id="add-item" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90">Add Line Item</button>
                <div class="text-lg font-semibold">
                    Grand Total: <span id="grand-total" class="text-primary">0.00</span>
                </div>
            </div>
        </div>

        <!-- Third Section: Supporting Documents -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Supporting Documents</h2>

            <label
                for="supporting_document"
                id="dropzone"
                class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer
               bg-gray-50 hover:bg-gray-100 transition text-center">
                <svg class="w-8 h-8 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16V12M7 12V8M7 12h4m6 4v-1a3 3 0 00-3-3H6a3 3 0 00-3 3v1" />
                </svg>

                <p class="text-sm text-gray-600" id="upload-text">
                    <span class="font-medium text-primary">Click to upload</span> or drag and drop
                </p>
                <p class="text-xs text-gray-500 mt-1">
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
            <x-button>Submit Budget</x-button>
        </div>
    </form>
</div>

<script>
    let itemCount = 1;

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
                <button type="button" class="remove-item bg-red-500 text-white px-3 py-2 rounded-md hover:bg-red-600">Remove</button>
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
        item.querySelector('.quantity').addEventListener('input', () => calculateTotal(item));
        item.querySelector('.unit-cost').addEventListener('input', () => calculateTotal(item));
    }

    // Attach listeners to initial item
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