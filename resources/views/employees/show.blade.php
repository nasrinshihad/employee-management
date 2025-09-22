<x-app-layout><x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Employee Details') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center mb-6">
                        @if ($employee->profile_picture)
                            <img src="{{ asset('storage/' . $employee->profile_picture) }}" alt="Profile Picture"
                                class="w-32 h-32 rounded-full mx-auto shadow-lg object-cover" style="width: 150px">
                        @else
                            <div
                                class="w-32 h-32 rounded-full bg-gray-200 mx-auto flex items-center justify-center text-gray-500">
                                <i class="bi bi-person-fill" style="font-size: 5rem;"></i>
                            </div>
                        @endif
                    </div>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>

                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-600">Full Name</dt>
                                <dd class="mt-1 text-lg text-gray-900 font-semibold">{{ $employee->name }}</dd>
                            </div>

                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-600">Email Address</dt>
                                <dd class="mt-1 text-lg text-gray-900">{{ $employee->email }}</dd>
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-600">Department</dt>
                                <dd class="mt-1 text-lg text-gray-900 font-semibold">{{ $employee->department }}</dd>
                            </div>

                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-600">Salary</dt>
                                <dd class="mt-1 text-lg text-green-600 font-bold">
                                    ${{ number_format($employee->salary, 2) }}
                                </dd>
                            </div>

                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-600">Joining Date</dt>
                                <dd class="mt-1 text-lg text-gray-900">
                                    {{ \Carbon\Carbon::parse($employee->joining_date)->format('M d, Y') }}
                                </dd>
                            </div>

                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-600">Role</dt>
                                <dd class="mt-1">
                                    @foreach ($employee->user->roles as $role)
                                        <span
                                            class="inline-block px-3 py-1 text-sm font-medium bg-indigo-100 text-indigo-700 rounded-full shadow-sm mr-2">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </dd>
                            </div>
                        </div>
                    </dl>

                    <div class="flex justify-end mt-6">
                        @if (!Auth::user()->hasRole('Employee'))
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                {{ __('Back to Employees') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
