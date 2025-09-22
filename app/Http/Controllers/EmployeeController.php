<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Employee;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Mail\WelcomeEmployee;
use App\Exports\EmployeesExport;
use App\Imports\EmployeesImport;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DataTables $dataTables)
    {
        if ($request->ajax()) {
            $query = Employee::with('user');

            try {
                $response = $dataTables->eloquent($query)
                    ->addIndexColumn()
                    ->addColumn('name', function ($employee) {
                        return $employee->user ? $employee->user->name : 'N/A';
                    })
                    ->addColumn('email', function ($employee) {
                        return $employee->user ? $employee->user->email : 'N/A';
                    })
                   ->addColumn('action', function($employee){
                        $btn  = '<a href="'.route('employees.show', $employee->id).'" class="btn btn-primary btn-sm me-1"><i class="bi bi-eye"></i></a>';
                        $btn .= '<a href="'.route('employees.edit', $employee->id).'" class="btn btn-info btn-sm me-1"><i class="bi bi-pencil"></i></a>';

                        if (!auth()->user()->hasRole('Manager')) {
                            $btn .= '<a href="'.route('employees.destroy', $employee->id).'" class="btn btn-danger btn-sm btn-delete" data-id="'.$employee->id.'"><i class="bi bi-trash"></i></a>';
                        }

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);

                return $response;
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'draw' => $request->input('draw', 0),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => []
                ]);
            }
        }

        return view('employees.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::whereIn('name', ['Admin', 'Manager'])->get();
        return view('employees.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:employees,email',
            'department' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'joining_date' => 'required|date',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'required|in:Employee,Manager',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        try {
            $employee = Employee::createWithUser([
                'name' => $request->name,
                'email' => $request->email,
                'department' => $request->department,
                'salary' => $request->salary,
                'joining_date' => $request->joining_date,
                'profile_picture' => $profilePicturePath,
                'password' => $request->password,
            ], $request->role);

            return redirect()->route('employees.index')
                ->with('success', 'Employee created successfully. They can now login with their email.');

        } catch (\Exception $e) {
            \Log::error("error creating employees ". $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load('user.roles');

        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $employee->load('user.roles');
        $roles = Role::whereIn('name', ['Admin', 'Manager'])->get();
        return view('employees.edit', compact('employee', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user_id . '|unique:employees,email,' . $employee->id,
            'department' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'joining_date' => 'required|date',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'required|in:Employee,Manager',
        ]);

        // Handle file upload
        $profilePicturePath = $employee->profile_picture;
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($employee->profile_picture) {
                Storage::disk('public')->delete($employee->profile_picture);
            }
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        \DB::transaction(function () use ($request, $employee, $profilePicturePath) {
            // Update employee
            $employee->update([
                'name' => $request->name,
                'email' => $request->email,
                'department' => $request->department,
                'salary' => $request->salary,
                'joining_date' => $request->joining_date,
                'profile_picture' => $profilePicturePath,
            ]);

            // Update user
            $employee->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update role
            $employee->user->syncRoles([$request->role]);
        });

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Employee $employee)
    {
        try {
            DB::transaction(function () use ($employee) {
                if ($employee->user) {
                    $employee->user->delete();
                }
                $employee->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting employee: '.$e->getMessage()
            ], 500);
        }
    }

    /**
     * Export employees From Excel or CSV
     *
     * @param Request $request
     * @return void
     */
    public function export(Request $request, Excel $excel)
    {
        $format = $request->get('format', 'xlsx');
        $filename = 'employees_' . date('Y-m-d_His') . '.' . $format;

        if ($format === 'csv') {
            return $excel->download(new EmployeesExport($format), $filename, Excel::CSV, [
                'Content-Type' => 'text/csv',
            ]);
        }

        return $excel->download(new EmployeesExport($format), $filename, Excel::XLSX);
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('employees.import');
    }

    /**
     * Import employees from Excel/CSV
     */
    public function import(Request $request, Excel $excel)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048'
    ]);

    $import = new EmployeesImport();

    try {
        $excel->import($import, $request->file('file'));

        $importedCount = $import->getImportedCount();
        $failedImports = $import->getFailedImports();

        $message = "Successfully imported {$importedCount} employees.";

        return redirect()->route('employees.index')
            ->with('success', $message)
            ->with('failed_imports', $failedImports);

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Import failed: ' . $e->getMessage());
    }
}

}
