@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-main mb-1">Budget Request Submission</h1>
        <p class="text-main font-medium">Submit a new budget request by filling out the details below. Include all necessary line items and supporting documentation.</p>
    </div>

    @if (session('success'))
    <div
        id="success-alert"
        class="mb-6 flex items-center justify-between rounded-lg bg-green-100 border border-green-300 p-4 text-green-800">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-sm font-medium">
                {{ session('success') }}
            </span>
        </div>

        <button
            type="button"
            onclick="document.getElementById('success-alert').remove()"
            class="text-green-700 hover:text-green-900 text-sm font-semibold">
            ✕
        </button>
    </div>
    @endif

    <form action="{{ route('admin.budget.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- First Section: Budget Details -->
        <div class="bg-orange-brown p-6 rounded-lg border border-primary overflow-hidden shadow-sm text-white">
            <h2 class="text-2xl font-semibold text-white mb-4">Budget Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="department_id" class="block text-sm font-medium text-white mb-2">Department</label>
                    <select name="department_id" id="department_id" class="w-full border border-white/70 rounded-md text-primary p-2 text-sm bg-input" required>
                        <option class="text-primary" value="">Select Department</option>
                        @foreach($departments as $department)
                        <option class="text-primary" value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-input-fields class="bg-input text-black" name="title" label="Title" type="text" placeholder="ex. Shared Budget" />
                <div>
    <label for="fiscal_year" class="block text-sm font-medium text-white mb-2">Fiscal Year</label>
    <select name="fiscal_year" id="fiscal_year" class="w-full border border-white/70 rounded-md text-black p-2 text-sm bg-input" required>
        @php
            $currentYear = date('Y');
        @endphp
        {{-- This loops from the current year to 5 years in the future --}}
        @for ($i = 0; $i <= 5; $i++)
            <option value="{{ $currentYear + $i }}">{{ $currentYear + $i }}</option>
        @endfor
    </select>
</div>
                <x-input-fields class="bg-input text-black" name="category" label="Budget Category" type="text" />
                <x-input-fields class="bg-input text-black cursor-pointer" name="submission_date" label="Due Date" type="date" min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" />
            </div>
            <div class="mt-6">
                <label for="justification" class="block text-sm font-medium text-white mb-2">Justification</label>
               <textarea name="justification" id="justification" rows="4" class="w-full border bg-input border-black/20 rounded-md p-2 text-sm text-black"></textarea>
            </div>
        </div>

        <!-- Second Section: Budget Line Items -->
        <div class="bg-orange-brown w-full p-6 rounded-lg border border-primary overflow-hidden shadow-sm text-white">
            <h2 class="text-xl font-semibold text-white mb-6">Budget Line Items</h2>
            <div id="line-items" class="space-y-4 w-full">
                <div class="line-item grid grid-cols-1 md:grid-cols-5 gap-4 items-end w-full">
                    <x-input-fields name="line_items[0][description]" label="Description" type="text" class="bg-input text-black" />
                    <article class="w-full">
                        <label class="text-sm font-medium">Quantity</label>
                        <input type="number" name="line_items[0][quantity]" min="1" class="w-full bg-input border border-black/20 rounded-md p-2 text-sm text-black quantity" required>
                    </article>
                    <article class="w-full">
                        <label class="text-sm font-medium">Unit Cost</label>
                        <input type="number" step="0.01" name="line_items[0][unit_cost]" min="0" class="w-full bg-input border border-black/20 rounded-md p-2 text-sm text-black unit-cost" required>
                    </article>
                    <article class="w-full">
                        <label class="text-sm font-medium">Total Cost</label>
                        <input type="number" step="0.01" class="w-full bg-input border border-black/20 rounded-md p-2 text-sm text-black total-cost" readonly>
                    </article>
                    <div class="flex items-end">
                        <button type="button" class="remove-item bg-primary text-white px-2 py-2 rounded-md hover:bg-red-600 hidden">❌</button>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex justify-between items-center">
                <button type="button" id="add-item"  class="text-white px-4 text-sm py-2 rounded-md bg-primary hover:bg-secondary">Add Line Item</button>
                <div class="text-lg font-semibold">
                    Grand Total: <span id="grand-total" class="text-sm text-white">0.00</span>
                </div>
            </div>
        </div>

        <!-- Third Section: Supporting Documents -->
        <div class="bg-orange-brown p-6 rounded-lg border border-primary overflow-hidden shadow-sm ">
            <h2 class="text-xl font-semibold text-white mb-6">Supporting Documents</h2>
            <label
                for="supporting_document"
                id="dropzone"
                class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-primary rounded-lg cursor-pointer
               bg-primary hover:bg-primary/80 transition text-center">

                <svg class="w-8 h-8 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16V12M7 12V8M7 12h4m6 4v-1a3 3 0 00-3-3H6a3 3 0 00-3 3v1" />
                </svg>

                <p class="text-sm text-white" id="upload-text">
                    <span class="font-medium">Click to upload</span> or drag and drop
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

            <!-- File Preview -->
            <div id="file-preview" class="hidden mt-4 flex items-center justify-between bg-primary/80 rounded-md px-4 py-2">
                <span id="file-name" class="text-sm text-white truncate"></span>
                <button
                    type="button"
                    id="remove-file"
                    class="px-2 py-2 rounded-md bg-primary hover:bg-red-300">❌
                </button>
            </div>
        </div>

        <div class="flex justify-end font-medium">
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
    // Format with commas and 2 decimal places
    document.getElementById('grand-total').textContent = grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

    document.getElementById('add-item').addEventListener('click', function() {
        const lineItems = document.getElementById('line-items');
        const newItem = document.createElement('div');
        newItem.className = 'line-item grid grid-cols-1 md:grid-cols-5 gap-4 items-end';
        newItem.innerHTML = `
            <article>
                <label class="text-sm font-medium">Description</label>
                <input type="text" name="line_items[${itemCount}][description]" class="w-full bg-input border border-white/60 rounded-md p-2 text-sm description text-primary" required>
            </article>
            <article>
                <label class="text-sm font-medium">Quantity</label>
                <input type="number" name="line_items[${itemCount}][quantity]" min="1" class="w-full bg-input border border-white/60 rounded-md p-2 text-sm quantity text-primary quantity" required>
            </article>
            <article>
                <label class="text-sm font-medium">Unit Cost</label>
                <input type="number" step="0.01" name="line_items[${itemCount}][unit_cost]" min="0" class="w-full bg-input border border-white/60 rounded-md p-2 text-sm quantity text-primary unit-cost" required>
            </article>
            <article>
                <label class="text-sm font-medium">Total Cost</label>
                <input type="number" step="0.01" class="w-full bg-input border border-white/60 rounded-md p-2 text-sm quantity text-primary total-cost" readonly>
            </article>
            <div class="flex items-end">
                <button type="button" class="remove-item bg-primary text-white px-2 py-2 rounded-md hover:bg-red-600">❌</button>
            </div>
        `;
        lineItems.appendChild(newItem);
        itemCount++;
        updateRemoveButtons();
        attachEventListeners(newItem);
    });

document.addEventListener('click', function (e) {
    if (e.target.type === 'date' && typeof e.target.showPicker === 'function') {
        e.target.showPicker();
    }
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

    const fileInput = document.getElementById('supporting_document');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const removeBtn = document.getElementById('remove-file');

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            fileName.textContent = fileInput.files[0].name;
            filePreview.classList.remove('hidden');
            uploadText.innerHTML = '<span class="font-medium">File uploaded</span>';
        }
    });

    removeBtn.addEventListener('click', () => {
        fileInput.value = '';
        filePreview.classList.add('hidden');
        uploadText.innerHTML = '<span class="font-medium">Click to upload</span> or drag and drop';
    });

    ['dragleave', 'drop'].forEach(event => {
        dropzone.addEventListener(event, e => {
            e.preventDefault();
            dropzone.classList.remove('border-primary', 'bg-primary/5');
        });
    });
</script>
@endsection
