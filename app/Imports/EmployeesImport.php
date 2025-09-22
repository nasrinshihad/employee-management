<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Employee;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class EmployeesImport implements ToCollection, WithHeadingRow
{
    private $importedCount = 0;
    private $failedImports = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $row = $row->toArray();

            try {
                if (empty($row['email'])) {
                    $this->failedImports[] = ['row'=>$row,'reason'=>'Email missing'];
                    continue;
                }

                $user = User::firstOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['name'],
                        'password' => Hash::make($row['password'] ?? 'password'),
                    ]
                );

                $roleName = $row['role'] ?? 'Employee';
                $role = Role::where('name', $roleName)->first();
                if ($role && !$user->hasRole($roleName)) {
                    $user->assignRole($role);
                }

                Employee::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'department' => $row['department'] ?? null,
                        'salary' => $row['salary'] ?? 0,
                        'joining_date' => $this->transformDate($row['joining_date'] ?? null),
                        'profile_picture' => $row['profile_picture'] ?? null,
                    ]
                );

                $this->importedCount++;

            } catch (\Exception $e) {
                $this->failedImports[] = ['row'=>$row,'reason'=>$e->getMessage()];
            }
        }
    }

    private function transformDate($value)
    {
        if (!$value) return null;
        return is_numeric($value) 
            ? Date::excelToDateTimeObject($value)->format('Y-m-d')
            : Carbon::parse($value)->format('Y-m-d');
    }

    public function getImportedCount() { return $this->importedCount; }
    public function getFailedImports() { return $this->failedImports; }
}
