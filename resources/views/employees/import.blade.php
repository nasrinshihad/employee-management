<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Employees') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Import Instructions</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Upload an Excel or CSV file with employee data. The file should include the following columns:
                        </p>
                        <ul class="list-disc list-inside text-sm text-gray-600 mb-4">
                            <li><strong>Name</strong> (Required)</li>
                            <li><strong>Email</strong> (Required, Unique)</li>
                            <li><strong>Department</strong> (Required)</li>
                            <li><strong>Salary</strong> (Required, Numeric)</li>
                            <li><strong>Joining Date</strong> (Required, Date format: YYYY-MM-DD)</li>
                            <li><strong>Profile Picture</strong> (Optional)</li>
                        </ul>
                    </div>

                    <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="file" :value="__('Select File')" />
                            <x-text-input id="file" class="block mt-1 w-full" type="file" name="file" required accept=".xlsx,.xls,.csv" />
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                            <p class="text-sm text-gray-500 mt-1">Accepted formats: .xlsx, .xls, .csv (Max: 2MB)</p>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('employees.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                <i class="bi bi-upload me-2"></i>{{ __('Import Employees') }}
                            </x-primary-button>
                        </div>
                    </form>

                    @if(isset($failed_imports) && count($failed_imports) > 0)
                    <div class="mt-6">
                        <h4 class="text-md font-medium text-red-600 mb-2">Failed Imports</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Row Data</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($failed_imports as $failed)
                                    <tr>
                                        <td>
                                            <pre class="text-xs">{{ json_encode($failed['row'], JSON_PRETTY_PRINT) }}</pre>
                                        </td>
                                        <td class="text-red-600">{{ $failed['reason'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>