@extends('layouts.app')

@section('title', 'Edit ' . $user->name)

@section('content')
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700">Users</a></li>
                <li class="text-gray-400">/</li>
                <li><a href="{{ route('users.show', $user) }}"
                        class="text-gray-500 hover:text-gray-700">{{ $user->name }}</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-900 font-medium">Edit</li>
            </ol>
        </nav>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">Edit User</h1>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden max-w-2xl">
        <form action="{{ route('users.update', $user) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Account Information --}}
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Account Information</h3>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-500 @enderror"
                        required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Change Password --}}
            <div class="space-y-4 pt-4 border-t">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Change Password <span
                        class="text-sm font-normal text-gray-500">(Leave blank to keep current)</span></h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" id="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New
                            Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>
            {{-- Personal Details --}}
            <div class="space-y-4 pt-4 border-t">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Personal Details <span
                        class="text-sm font-normal text-gray-500">(Optional)</span></h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->detail?->phone) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth"
                            value="{{ old('date_of_birth', $user->detail?->date_of_birth?->format('Y-m-d')) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('date_of_birth') border-red-500 @enderror">
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea name="address" id="address" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('address') border-red-500 @enderror">{{ old('address', $user->detail?->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $user->detail?->city) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('city') border-red-500 @enderror">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">State / Province</label>
                        <input type="text" name="state" id="state" value="{{ old('state', $user->detail?->state) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('state') border-red-500 @enderror">
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code"
                            value="{{ old('postal_code', $user->detail?->postal_code) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('postal_code') border-red-500 @enderror">
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                        <input type="text" name="country" id="country" value="{{ old('country', $user->detail?->country) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('country') border-red-500 @enderror">
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('users.show', $user) }}"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
                    Update User
                </button>
            </div>
        </form>
    </div>
@endsection