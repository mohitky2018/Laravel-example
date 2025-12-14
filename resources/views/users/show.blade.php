@extends('layouts.app')

@section('title', $user->name)

@section('content')
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700">Users</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-900 font-medium">{{ $user->name }}</li>
            </ol>
        </nav>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden max-w-2xl">
        {{-- User Header --}}
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex items-center">
                <div class="h-16 w-16 flex-shrink-0">
                    <span class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-indigo-600 font-medium text-xl">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </span>
                </div>
                <div class="ml-5">
                    <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        {{-- Account Information --}}
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Account Information</h3>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">User ID</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->id }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email Verified</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if ($user->email_verified_at)
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Verified on {{ $user->email_verified_at->format('M d, Y') }}
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Not verified
                            </span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y \a\t h:i A') }}</dd>
                </div>
            </dl>
        </div>

        {{-- Personal Details --}}
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Personal Details</h3>
            @if ($user->detail)
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    @if ($user->detail->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->detail->phone }}</dd>
                        </div>
                    @endif
                    @if ($user->detail->date_of_birth)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->detail->date_of_birth->format('M d, Y') }}</dd>
                        </div>
                    @endif
                    @if ($user->detail->address)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->detail->address }}</dd>
                        </div>
                    @endif
                    @if ($user->detail->city || $user->detail->state)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">City / State</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ collect([$user->detail->city, $user->detail->state])->filter()->join(', ') }}
                            </dd>
                        </div>
                    @endif
                    @if ($user->detail->postal_code)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Postal Code</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->detail->postal_code }}</dd>
                        </div>
                    @endif
                    @if ($user->detail->country)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Country</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->detail->country }}</dd>
                        </div>
                    @endif
                </dl>
            @else
                <p class="text-sm text-gray-500 italic">No personal details provided.</p>
            @endif
        </div>

        {{-- Actions --}}
        <div class="px-6 py-4 bg-gray-50 flex items-center justify-between">
            <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Users
            </a>
            <div class="flex items-center space-x-3">
                <a href="{{ route('users.edit', $user) }}"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
                    Edit User
                </a>
                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                    onsubmit="return confirm('Are you sure you want to delete this user?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection