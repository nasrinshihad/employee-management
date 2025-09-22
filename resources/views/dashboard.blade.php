<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold">Welcome, {{ Auth::user()->name }}!</h3>
                        </div>
                        <div class="bg-indigo-100 text-indigo-800 px-4 py-2 rounded-full text-sm font-medium">
                            {{ Auth::user()->getRoleNames()->first() }}
                        </div>
                    </div>
                </div>
            </div>

            @role('Employee')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 text-center">
                        <h3 class="text-xl font-semibold">You are logged in!</h3>
                        <p class="text-gray-600 mt-2">View and manage your own profile from the navigation menu.</p>
                    </div>
                </div>
            @endrole

            @hasanyrole('Admin|Manager')
                    @role('Admin')
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" style="height: 150px;">
                            <div class="p-6 text-gray-900">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <i class="bi bi-person-gear text-2xl me-2 text-blue-600"></i>
                                        <h3 class="text-lg font-semibold">User Management</h3>
                                    </div>
                                    <a href="{{ route('users.index') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                                <p class="text-gray-600">Create and manage all users and their roles.</p>
                            </div>
                        </div>
                    @endrole

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" style="height: 150px;">
                        <div class="p-6 text-gray-900">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <i class="bi bi-people-fill text-2xl me-2 text-green-600"></i>
                                    <h3 class="text-lg font-semibold">Employee Management</h3>
                                </div>
                                <a href="{{ route('employees.index') }}" class="btn btn-success btn-sm">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                            <p class="text-gray-600">Create and manage employee records.</p>
                        </div>
                    </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Quick Overview</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ \App\Models\User::role('Employee')->count() }}</div>
                                <div class="text-blue-800">Total Employees</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ \App\Models\User::role('Manager')->count() }}</div>
                                <div class="text-green-800">Managers</div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ \App\Models\User::count() }}</div>
                                <div class="text-purple-800">Total Users</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endhasanyrole

        </div>
    </div>
</x-app-layout>