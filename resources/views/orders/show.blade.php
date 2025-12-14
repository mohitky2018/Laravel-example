@extends('layouts.app')

@section('title', 'Order #' . str_pad($order->id, 5, '0', STR_PAD_LEFT))

@section('content')
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
            &larr; Back to Orders
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h2>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <div class="p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-4">Order Items</h3>
                    <div class="overflow-hidden border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right text-sm text-gray-500">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Total</td>
                                    <td class="px-4 py-3 text-right text-lg font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if ($order->notes)
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Notes</h3>
                            <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Customer Details -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">Customer Details</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center">
                        <span class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-indigo-600 font-medium">{{ strtoupper(substr($order->user->name, 0, 2)) }}</span>
                        </span>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $order->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">Update Status</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <select name="status"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @foreach (\App\Models\Order::getStatuses() as $status)
                                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Info -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">Order Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </div>
            </div>

            <!-- Delete Order -->
            <div class="bg-white rounded-lg shadow overflow-hidden border border-red-200">
                <div class="p-6">
                    <h3 class="text-sm font-medium text-red-700 mb-4">Danger Zone</h3>
                    <form action="{{ route('orders.destroy', $order) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this order? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-md hover:bg-red-100">
                            Delete Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
