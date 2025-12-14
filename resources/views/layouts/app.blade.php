<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Enhanced Form Input Styling */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="tel"],
        input[type="date"],
        input[type="url"],
        textarea {
            border: 1.5px solid #d1d5db !important;
            background-color: #fff;
            padding: 0.625rem 0.875rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            line-height: 1.5;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        /* Enhanced Select Field Styling */
        select {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            border: 1.5px solid #d1d5db !important;
            background-color: #fff !important;
            padding: 0.625rem 2.5rem 0.625rem 0.875rem !important;
            border-radius: 0.5rem !important;
            transition: all 0.2s ease !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            height: auto !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
            background-position: right 0.5rem center !important;
            background-repeat: no-repeat !important;
            background-size: 1.5em 1.5em !important;
            cursor: pointer !important;
        }

        input[type="text"]:hover,
        input[type="email"]:hover,
        input[type="password"]:hover,
        input[type="number"]:hover,
        input[type="tel"]:hover,
        input[type="date"]:hover,
        input[type="url"]:hover,
        select:hover,
        textarea:hover {
            border-color: #9ca3af !important;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus,
        input[type="tel"]:focus,
        input[type="date"]:focus,
        input[type="url"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15), 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        input::placeholder,
        textarea::placeholder {
            color: #9ca3af;
        }

        /* Error state styling */
        input.border-red-500,
        select.border-red-500,
        textarea.border-red-500 {
            border-color: #ef4444 !important;
        }

        input.border-red-500:focus,
        select.border-red-500:focus,
        textarea.border-red-500:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15), 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        /* Enhanced checkboxes */
        input[type="checkbox"] {
            border: 1.5px solid #d1d5db !important;
            border-radius: 0.25rem;
            width: 1.125rem;
            height: 1.125rem;
            transition: all 0.2s ease;
        }

        input[type="checkbox"]:checked {
            background-color: #6366f1;
            border-color: #6366f1 !important;
        }

        input[type="checkbox"]:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        /* Form labels enhancement */
        label {
            font-weight: 500;
            color: #374151;
        }

        /* Card enhancements */
        .bg-white {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }

        /* Button enhancements */
        button[type="submit"],
        .btn-primary {
            transition: all 0.2s ease;
            font-weight: 500;
        }

        button[type="submit"]:hover,
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.3);
        }

        /* Remove default appearance for number inputs */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        /* Mobile menu slide animation */
        #mobile-menu {
            transition: transform 0.3s ease-in-out;
        }

        #mobile-menu.hidden {
            transform: translateX(-100%);
        }

        #mobile-menu:not(.hidden) {
            transform: translateX(0);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-gray-100 min-h-screen">
    <nav class="bg-white/80 backdrop-blur-md shadow-lg border-b border-gray-200/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Mobile menu button -->
                    <button type="button" id="mobile-menu-btn"
                        class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 mr-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Logo -->
                    <a href="{{ url('/') }}"
                        class="flex items-center gap-2 text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent hover:from-indigo-500 hover:to-purple-500 transition-all duration-200">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span class="hidden sm:inline">{{ config('app.name', 'Laravel') }}</span>
                    </a>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex md:ml-10 items-baseline space-x-1">
                        <a href="{{ route('users.index') }}"
                            class="{{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent' }} flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Users
                        </a>
                        <a href="{{ route('products.index') }}"
                            class="{{ request()->routeIs('products.*') ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent' }} flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Products
                        </a>
                        <a href="{{ route('orders.index') }}"
                            class="{{ request()->routeIs('orders.*') ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent' }} flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            Orders
                        </a>
                    </div>
                </div>

                <!-- User Info & Logout -->
                <div class="flex items-center gap-2">
                    @auth
                        <div class="hidden sm:flex items-center gap-3">
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-lg">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="hidden sm:inline">Logout</span>
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu"
            class="hidden md:hidden absolute left-0 top-16 w-64 h-screen bg-white shadow-xl border-r border-gray-200 z-50">
            <div class="px-4 py-6 space-y-2">
                @auth
                    <div class="flex items-center gap-3 px-3 py-3 mb-4 bg-gray-50 rounded-lg">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                @endauth

                <a href="{{ url('/') }}"
                    class="{{ request()->is('/') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('users.index') }}"
                    class="{{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Users
                </a>
                <a href="{{ route('products.index') }}"
                    class="{{ request()->routeIs('products.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Products
                </a>
                <a href="{{ route('orders.index') }}"
                    class="{{ request()->routeIs('orders.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }} flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Orders
                </a>

                @auth
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-3 py-3 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Overlay for mobile menu -->
        <div id="mobile-menu-overlay" class="hidden fixed inset-0 bg-black/50 z-40 md:hidden"></div>
    </nav>

    <main class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

        function toggleMobileMenu() {
            mobileMenu.classList.toggle('hidden');
            mobileMenuOverlay.classList.toggle('hidden');
        }

        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
        mobileMenuOverlay.addEventListener('click', toggleMobileMenu);
    </script>
</body>

</html>