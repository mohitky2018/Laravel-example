@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="mb-6">
        <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
            &larr; Back to Products
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h2>
            <div class="space-x-2">
                <a href="{{ route('products.edit', $product) }}"
                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Edit
                </a>
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline"
                    onsubmit="return confirm('Are you sure you want to delete this product?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center px-3 py-1.5 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $product->name }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Price</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-semibold">${{ number_format($product->price, 2) }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Stock</dt>
                    <dd class="mt-1">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->stock > 10 ? 'bg-green-100 text-green-800' : ($product->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $product->stock }} in stock
                        </span>
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $product->description ?: 'No description provided.' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('M d, Y H:i') }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('M d, Y H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>
@endsection