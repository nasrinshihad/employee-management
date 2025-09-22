<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Employee') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('employees.update', $employee->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-medium mb-4">Personal Information</h3>

                                <div class="mb-4">
                                    <x-input-label for="name" :value="__('Full Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                        :value="old('name', $employee->user->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="email" :value="__('Email Address')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                        :value="old('email', $employee->user->email)" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="department" :value="__('Department')" />
                                    <x-text-input id="department" class="block mt-1 w-full" type="text"
                                        name="department" :value="old('department', $employee->department)" required />
                                    <x-input-error :messages="$errors->get('department')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="salary" :value="__('Salary')" />
                                    <x-text-input id="salary" class="block mt-1 w-full" type="number" name="salary"
                                        :value="old('salary', $employee->salary)" required />
                                    <x-input-error :messages="$errors->get('salary')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="joining_date" :value="__('Joining Date')" />
                                    <x-text-input id="joining_date" class="block mt-1 w-full" type="date"
                                        name="joining_date" :value="old('joining_date', $employee->joining_date)" required />
                                    <x-input-error :messages="$errors->get('joining_date')" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium mb-4">Role & Profile</h3>

                                <div class="mb-4">
                                    <x-input-label for="role" :value="__('Role')" />
                                    <select id="role" name="role"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required>
                                        <option value="Employee" @selected(old('role', $employee->role) === 'Employee')>Employee</option>
                                        <option value="Manager" @selected(old('role', $employee->role) === 'Manager')>Manager</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="profile_picture" :value="__('Profile Picture')" />
                                    <input id="profile_picture" type="file" name="profile_picture"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                                    <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />

                                    @if ($employee->profile_picture)
                                        <div class="mt-3">
                                            <img src="{{ asset('storage/' . $employee->profile_picture) }}"
                                                alt="Profile Picture" class="h-24 w-24 rounded-full object-cover" style="width: 100px">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('employees.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Employee') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
