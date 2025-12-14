@extends('layouts.app')

@section('title', 'Create Order')

@section('content')
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
            &larr; Back to Orders
        </a>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Create New Order</h2>
        </div>

        <form action="{{ route('orders.store') }}" method="POST" class="p-6 space-y-6" id="order-form">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700">Customer</label>
                    <select name="user_id" id="user_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('user_id') border-red-500 @enderror"
                        required>
                        <option value="">Select a customer</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <input type="text" name="notes" id="notes" value="{{ old('notes') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Optional order notes">
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                    <button type="button" id="add-item"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Item
                    </button>
                </div>

                @error('items')
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ $message }}
                    </div>
                @enderror

                <div id="items-container" class="space-y-4">
                    <div class="item-row grid grid-cols-12 gap-4 items-end p-4 bg-gray-50 rounded-lg">
                        <div class="col-span-6">
                            <label class="block text-sm font-medium text-gray-700">Product</label>
                            <select name="items[0][product_id]"
                                class="product-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required>
                                <option value="">Select a product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}"
                                        data-stock="{{ $product->stock }}">
                                        {{ $product->name }} - ${{ number_format($product->price, 2) }} ({{ $product->stock }}
                                        in stock)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" name="items[0][quantity]"
                                class="quantity-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                min="1" value="1" required>
                        </div>
                        <div class="col-span-2 text-right">
                            <span class="item-subtotal text-sm font-medium text-gray-900">$0.00</span>
                        </div>
                        <div class="col-span-1">
                            <button type="button" class="remove-item text-red-600 hover:text-red-900 p-2"
                                style="display: none;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-gray-100 rounded-lg flex justify-between items-center">
                    <span class="text-lg font-medium text-gray-900">Order Total:</span>
                    <span id="order-total" class="text-xl font-bold text-gray-900">$0.00</span>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('orders.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700">
                    Create Order
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let itemIndex = 1;
            const container = document.getElementById('items-container');
            const addButton = document.getElementById('add-item');

            function updateCalculations() {
                let total = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const select = row.querySelector('.product-select');
                    const quantity = row.querySelector('.quantity-input');
                    const subtotalEl = row.querySelector('.item-subtotal');

                    if (select.value && quantity.value) {
                        const option = select.options[select.selectedIndex];
                        const price = parseFloat(option.dataset.price) || 0;
                        const qty = parseInt(quantity.value) || 0;
                        const subtotal = price * qty;
                        subtotalEl.textContent = '$' + subtotal.toFixed(2);
                        total += subtotal;
                    } else {
                        subtotalEl.textContent = '$0.00';
                    }
                });
                document.getElementById('order-total').textContent = '$' + total.toFixed(2);
                updateRemoveButtons();
            }

            function updateRemoveButtons() {
                const rows = document.querySelectorAll('.item-row');
                rows.forEach((row, index) => {
                    const removeBtn = row.querySelector('.remove-item');
                    removeBtn.style.display = rows.length > 1 ? 'block' : 'none';
                });
            }

            addButton.addEventListener('click', function () {
                const template = container.querySelector('.item-row').cloneNode(true);
                template.querySelector('.product-select').name = `items[${itemIndex}][product_id]`;
                template.querySelector('.product-select').value = '';
                template.querySelector('.quantity-input').name = `items[${itemIndex}][quantity]`;
                template.querySelector('.quantity-input').value = '1';
                template.querySelector('.item-subtotal').textContent = '$0.00';
                container.appendChild(template);
                itemIndex++;
                updateRemoveButtons();

                // Add event listeners to new elements
                template.querySelector('.product-select').addEventListener('change', updateCalculations);
                template.querySelector('.quantity-input').addEventListener('input', updateCalculations);
                template.querySelector('.remove-item').addEventListener('click', function () {
                    template.remove();
                    updateCalculations();
                });
            });

            // Initial event listeners
            document.querySelectorAll('.product-select').forEach(el => {
                el.addEventListener('change', updateCalculations);
            });
            document.querySelectorAll('.quantity-input').forEach(el => {
                el.addEventListener('input', updateCalculations);
            });
            document.querySelectorAll('.remove-item').forEach(el => {
                el.addEventListener('click', function () {
                    this.closest('.item-row').remove();
                    updateCalculations();
                });
            });

            updateCalculations();
        });
    </script>
@endsection