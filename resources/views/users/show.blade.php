<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium mb-4">Personal Information</h3>
                            <div class="space-y-2">
                                <p><strong>Name:</strong> {{ $user->name }}</p>
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium mb-4">Role Information</h3>
                            <div class="space-y-2">
                                <p><strong>Roles:</strong>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary">{{ $role->name }}</span>
                                    @endforeach
                                </p>
                                <p><strong>Created At:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                                <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                            {{ __('Back to Users') }}
                        </a>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">
                            {{ __('Edit User') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>