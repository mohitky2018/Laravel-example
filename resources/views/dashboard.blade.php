@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- Welcome Header --}}
    <div class="mb-8">
        <div
            class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute inset-0 bg-grid-white/10 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.6))]">
            </div>
            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-2">Welcome to Dashboard</h1>
                <p class="text-indigo-100">Manage your users, products, and orders from one place.</p>
            </div>
            <div class="absolute right-8 top-1/2 -translate-y-1/2 hidden lg:block">
                <svg class="w-32 h-32 text-white/20" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Users Card --}}
        <div
            class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-indigo-100 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <a href="{{ route('users.index') }}"
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 group-hover:gap-2 transition-all">
                    View All
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">Users</h3>
            <p class="text-gray-500 text-sm">Manage user accounts and permissions</p>
            <a href="{{ route('users.create') }}"
                class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New User
            </a>
        </div>

        {{-- Products Card --}}
        <div
            class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-green-100 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <a href="{{ route('products.index') }}"
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 group-hover:gap-2 transition-all">
                    View All
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">Products</h3>
            <p class="text-gray-500 text-sm">Manage your product catalog</p>
            <a href="{{ route('products.create') }}"
                class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-emerald-600 hover:text-emerald-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Product
            </a>
        </div>

        {{-- Orders Card --}}
        <div
            class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-orange-100 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <a href="{{ route('orders.index') }}"
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 group-hover:gap-2 transition-all">
                    View All
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">Orders</h3>
            <p class="text-gray-500 text-sm">Track and manage customer orders</p>
            <a href="{{ route('orders.create') }}"
                class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-orange-600 hover:text-orange-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create New Order
            </a>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('users.create') }}"
                class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-blue-50 border-2 border-transparent hover:border-blue-200 transition-all duration-200 group">
                <div
                    class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-2 group-hover:bg-blue-200 transition-colors">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700">New User</span>
            </a>
            <a href="{{ route('products.create') }}"
                class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-emerald-50 border-2 border-transparent hover:border-emerald-200 transition-all duration-200 group">
                <div
                    class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mb-2 group-hover:bg-emerald-200 transition-colors">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-emerald-700">New Product</span>
            </a>
            <a href="{{ route('orders.create') }}"
                class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-orange-50 border-2 border-transparent hover:border-orange-200 transition-all duration-200 group">
                <div
                    class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mb-2 group-hover:bg-orange-200 transition-colors">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-orange-700">New Order</span>
            </a>
            <a href="{{ route('users.index') }}"
                class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-purple-50 border-2 border-transparent hover:border-purple-200 transition-all duration-200 group">
                <div
                    class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-2 group-hover:bg-purple-200 transition-colors">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-purple-700">View Reports</span>
            </a>
        </div>
    </div>
@endsection