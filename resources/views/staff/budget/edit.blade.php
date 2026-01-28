@extends('layouts.auth-layout')
@section('main-content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-black mb-1">Edit Budget Request</h1>
        <p class="text-black font-medium">Update the budget request details below. You can add or remove line items and replace supporting documents.</p>
    </div>
    
    <form action="{{ route('staff.budget.update', $budget->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- First Section: Budget Details -->
        <div class="bg-orange-200 p-6 rounded-lg shadow-sm text-primary">
            <h2 class="text-2xl font-semibold text-primary mb-4">Budget Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    @php $user = request()->user(); @endphp
                    <label for="department_id" class="block text-sm font-medium text-primary mb-2">Department</label>
                    <select id="department_id" class="w-full border border-white/70 rounded-md text-primary p-2 text-sm bg-white" disabled>
                        @foreach($departments as $department)
                        <option class="text-primary" value="{{ $department->id }}" {{ $department->id == ($budget->department_id ?? $user->department_id) ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="department_id" value="{{ old('department_id', $budget->department_id ?? $user->department_id) }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Title</label>
                    <input name="title" type="text" value="{{ old('title', $budget->title) }}" class="w-full border border-white/70 rounded-md p-2 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Fiscal Year</label>
                    <input name="fiscal_year" type="number" value="{{ old('fiscal_year', $budget->fiscal_year) }}" class="w-full border border-white/70 rounded-md p-2 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Budget Category</label>
                    <input name="category" type="text" value="{{ old('category', $budget->category) }}" class="w-full border border-white/70 rounded-md p-2 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Due Date</label>
                    <input name="submission_date" type="date" value="{{ old('submission_date', $budget->submission_date->format('Y-m-d')) }}" min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" class="w-full border border-white/70 rounded-md p-2 text-sm" />
                </div>
            </div>
            <div class="mt-6">
                <label for="justification" class="block text-sm font-medium text-primary mb-2">Justification</label>
                <textarea name="justification" id="justification" rows="4" class="w-full border bg-white border-black/20 rounded-md p-2 text-sm">{{ old('justification', $budget->justification) }}</textarea>
            </div>
        </div>

        <!-- Second Section: Budget Line Items -->
        <div class="bg-orange-200 w-full p-6 rounded-lg shadow-sm text-primary">
            <h2 class="text-xl font-semibold text-primary mb-6">Budget Line Items</h2>
            <div id="line-items" class="space-y-4 w-full">
                @php
                    $oldItems = old('line_items', $budget->lineItems->toArray());
                @endphp
                @if(count($oldItems) > 0)
                    @foreach($oldItems as $i => $it)
                        <div class="line-item grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                            <article>
                                <label class="text-sm font-medium">Description</label>
                                <input type="text" name="line_items[{{ $i }}][description]" value="{{ $it['description'] ?? '' }}" class="w-full bg-white border border-black/20 rounded-md p-2 text-sm" required>
                            </article>
                            <article>
                                <label class="text-sm font-medium">Quantity</label>
                                <input type="number" name="line_items[{{ $i }}][quantity]" min="1" value="{{ $it['quantity'] ?? 1 }}" class="w-full bg-white border border-black/20 rounded-md p-2 text-sm quantity" required>
                            </article>
                            <article>
                                <label class="text-sm font-medium">Unit Cost</label>
                                <input type="number" step="0.01" name="line_items[{{ $i }}][unit_cost]" min="0" value="{{ $it['unit_cost'] ?? 0 }}" class="w-full bg-white border border-black/20 rounded-md p-2 text-sm unit-cost" required>
                            </article>
                            <article>
                                <label class="text-sm font-medium">Total Cost</label>
                                <input type="number" step="0.01" class="w-full bg-white border border-black/20 rounded-md p-2 text-sm total-cost" readonly value="{{ number_format((($it['quantity'] ?? 0) * ($it['unit_cost'] ?? 0)), 2, '.', '') }}">
                            </article>
                            <div class="flex items-end">
                                <button type="button" class="remove-item bg-red-500 text-primary px-3 py-2 rounded-md hover:bg-red-600">Remove</button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="line-item grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        <article>
                            <label class="text-sm font-medium">Description</label>
                            <input type="text" name="line_items[0][description]" class="w-full bg-white border border-black/20 rounded-md p-2 text-sm" required>
                        </article>
                        <article>
                            <label class="text-sm font-medium">Quantity</label>
                            <input type="number" name="line_items[0][quantity]" min="1" class="w-full bg-white border border-black/20 rounded-md p-2 text-sm quantity" required>
                        </article>
                        <article>
                            <label class="text-sm font-medium">Unit Cost</label>
                            <input type="number" step="0.01" name="line_items[0][unit_cost]" min="0" class="w-full bg-white border border-black/20 rounded-md p-2 text-sm unit-cost" required>
                        </article>
                        <article>
                            <label class="text-sm font-medium">Total Cost</label>
                            <input type="number" step="0.01" class="w-full bg-white border border-black/20 rounded-md p-2 text-sm total-cost" readonly>
                        </article>
                        <div class="flex items-end">
                            <button type="button" class="remove-item bg-red-500 text-primary px-3 py-2 rounded-md hover:bg-red-600 hidden">Remove</button>
                        </div>
                    </div>
                @endif
            </div>
            <div class="mt-4 flex justify-between items-center">
                <button type="button" id="add-item" class="bg-primary text-white px-4 text-sm py-2 rounded-md hover:bg-opacity-90">Add Line Item</button>
                <div class="text-lg font-semibold">
                    Grand Total: <span id="grand-total" class="text-sm text-primary">0.00</span>
                </div>
            </div>
        </div>

        <!-- Third Section: Supporting Documents -->
        <div class="bg-orange-200 p-6 rounded-lg shadow-sm text-primary">
            <h2 class="text-xl font-semibold text-primary mb-6">Supporting Documents</h2>

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
            <div id="file-preview" class="mt-4 flex items-center justify-between bg-white rounded-md px-4 py-2">
                <div>
                    @if($budget->supporting_document)
                        <a href="{{ asset('storage/' . $budget->supporting_document) }}" target="_blank" id="file-name" class="text-sm text-gray-700 truncate">Current document</a>
                    @else
                        <span id="file-name" class="text-sm text-gray-700 truncate">No file selected</span>
                    @endif
                </div>
                <div>
                    <button type="button" id="remove-file" class="text-red-600 text-sm font-medium hover:underline">Remove</button>
                </div>
            </div>
        </div>
        <div class="flex justify-end">
            <x-button>Save Changes</x-button>
        </div>
    </form>
</div>

<script>
    // initialize itemCount to current number of line items
    let itemCount = document.querySelectorAll('#line-items .line-item').length;

    function calculateTotal(item) {
        const quantity = parseFloat(item.querySelector('.quantity').value) || 0;
        const unitCost = parseFloat(item.querySelector('.unit-cost').value) || 0;
        const total = quantity * unitCost;
        const totalInput = item.querySelector('.total-cost');
        if (totalInput) totalInput.value = total.toFixed(2);
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.total-cost').forEach(input => {
            grandTotal += parseFloat(input.value) || 0;
        });
        document.getElementById('grand-total').textContent = grandTotal.toFixed(2);
    }

    function attachEventListeners(item) {
        const qty = item.querySelector('.quantity');
        const unit = item.querySelector('.unit-cost');
        if (qty) qty.addEventListener('input', () => calculateTotal(item));
        if (unit) unit.addEventListener('input', () => calculateTotal(item));
    }

    document.getElementById('add-item').addEventListener('click', function() {
        const lineItems = document.getElementById('line-items');
        const newItem = document.createElement('div');
        newItem.className = 'line-item grid grid-cols-1 md:grid-cols-5 gap-4 items-end';
        newItem.innerHTML = `
            <article>
                <label class="text-sm font-medium">Description</label>
                <input type="text" name="line_items[${itemCount}][description]" class="w-full bg-white border border-white/60 rounded-md p-2 text-sm" required>
            </article>
            <article>
                <label class="text-sm font-medium">Quantity</label>
                <input type="number" name="line_items[${itemCount}][quantity]" min="1" class="w-full bg-white border border-white/60 rounded-md p-2 text-sm quantity" required>
            </article>
            <article>
                <label class="text-sm font-medium">Unit Cost</label>
                <input type="number" step="0.01" name="line_items[${itemCount}][unit_cost]" min="0" class="w-full bg-white border border-white/60 rounded-md p-2 text-sm unit-cost" required>
            </article>
            <article>
                <label class="text-sm font-medium">Total Cost</label>
                <input type="number" step="0.01" class="w-full bg-white border border-white/60 rounded-md p-2 text-sm total-cost" readonly>
            </article>
            <div class="flex items-end">
                <button type="button" class="remove-item bg-red-500 text-primary px-3 py-2 rounded-md hover:bg-red-600">Remove</button>
            </div>
        `;
        lineItems.appendChild(newItem);
        attachEventListeners(newItem);
        updateRemoveButtons();
        itemCount++;
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
            if (removeBtn) {
                if (items.length > 1) {
                    removeBtn.classList.remove('hidden');
                } else {
                    removeBtn.classList.add('hidden');
                }
            }
        });
    }

    // Attach listeners to initial items
    document.querySelectorAll('#line-items .line-item').forEach(item => {
        attachEventListeners(item);
    });

    // Initial calculation
    calculateGrandTotal();

    // File upload functionality
    const input = document.getElementById('supporting_document');
    const uploadText = document.getElementById('upload-text');
    const dropzone = document.getElementById('dropzone');

    if (input) {
        input.addEventListener('change', () => {
            if (input.files.length) {
                uploadText.innerHTML = `<span class="font-medium text-green-600">${input.files[0].name}</span> selected`;
                document.getElementById('file-name').textContent = input.files[0].name;
            } else {
                uploadText.innerHTML = `<span class="font-medium text-primary">Click to upload</span> or drag and drop`;
                document.getElementById('file-name').textContent = '{{ $budget->supporting_document ? "Current document" : "No file selected" }}';
            }
        });
    }

    // Drag styling
    ['dragenter', 'dragover'].forEach(event => {
        if (dropzone) dropzone.addEventListener(event, e => {
            e.preventDefault();
            dropzone.classList.add('border-primary', 'bg-primary/5');
        });
    });

    ['dragleave', 'drop'].forEach(event => {
        if (dropzone) dropzone.addEventListener(event, e => {
            e.preventDefault();
            dropzone.classList.remove('border-primary', 'bg-primary/5');
        });
    });

    const fileInput = document.getElementById('supporting_document');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const removeBtn = document.getElementById('remove-file');

    if (fileInput && removeBtn) {
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileName.textContent = fileInput.files[0].name;
                filePreview.classList.remove('hidden');
                uploadText.innerHTML = '<span class="font-medium">File selected</span>';
            }
        });

        removeBtn.addEventListener('click', () => {
            fileInput.value = '';
            fileName.textContent = '{{ $budget->supporting_document ? "Current document" : "No file selected" }}';
            uploadText.innerHTML = '<span class="font-medium">Click to upload</span> or drag and drop';
        });
    }
</script>
@endsection
