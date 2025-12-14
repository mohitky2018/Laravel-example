<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Welcome') - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Enhanced Form Input Styling */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"] {
            border: 1.5px solid #d1d5db !important;
            background-color: #fff !important;
            color: #1f2937 !important;
            /* Dark text for visibility */
            padding: 0.75rem 1rem !important;
            border-radius: 0.5rem !important;
            transition: all 0.2s ease !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }

        input[type="text"]:hover,
        input[type="email"]:hover,
        input[type="password"]:hover,
        input[type="number"]:hover {
            border-color: #9ca3af !important;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus {
            outline: none !important;
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15), 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }

        input::placeholder {
            color: #9ca3af;
        }

        /* Checkbox styling */
        input[type="checkbox"] {
            border: 1.5px solid #d1d5db !important;
            border-radius: 0.25rem;
            width: 1rem;
            height: 1rem;
            transition: all 0.2s ease;
        }

        input[type="checkbox"]:checked {
            background-color: #6366f1;
            border-color: #6366f1 !important;
        }
    </style>
</head>

<body
    class="bg-gradient-to-br from-indigo-900 via-purple-900 to-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 text-2xl font-bold text-white">
                <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                {{ config('app.name', 'Laravel') }}
            </a>
        </div>

        <!-- Card -->
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8">
            @yield('content')
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-400 text-sm mt-6">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>

</html>