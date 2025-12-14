@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <h2 class="text-2xl font-bold text-white text-center mb-6">Welcome Back</h2>

    @if ($errors->any())
        <div class="mb-4 bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-lg text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ url('/login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-200 mb-2">Email Address</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                class="w-full bg-white/10 border-white/30 text-white placeholder-gray-400" placeholder="you@example.com">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-200 mb-2">Password</label>
            <input type="password" name="password" id="password" required
                class="w-full bg-white/10 border-white/30 text-white placeholder-gray-400" placeholder="••••••••">
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-300">
                <input type="checkbox" name="remember" class="rounded bg-white/10 border-white/30">
                Remember me
            </label>
        </div>

        <button type="submit"
            class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
            Sign In
        </button>
    </form>

    <p class="text-center text-gray-400 text-sm mt-6">
        Don't have an account?
        <a href="{{ url('/register') }}" class="text-indigo-400 hover:text-indigo-300 font-medium">Create one</a>
    </p>
@endsection