<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $managerUser = User::firstOrCreate(
            ['email' => 'manager@gmail.com'],
            ['name' => 'Manager User', 'password' => bcrypt('manager123')]
        );
        $managerUser->assignRole('Manager');

        $employeeUser = User::firstOrCreate(
            ['email' => 'employee@gmail.com'],
            ['name' => 'Employee User', 'password' => bcrypt('employee123')]
        );
        $employeeUser->assignRole('Employee');

        Employee::updateOrCreate(
            ['user_id' => $managerUser->id],
            [
                'name' => $managerUser->name,
                'email' => $managerUser->email,
                'department' => 'Sales',
                'salary' => 60000,
                'joining_date' => now()->subMonths(2)->toDateString(),
                'profile_picture' => null
            ]
        );

        Employee::updateOrCreate(
            ['user_id' => $employeeUser->id],
            [
                'name' => $employeeUser->name,
                'email' => $employeeUser->email,
                'department' => 'Marketing',
                'salary' => 40000,
                'joining_date' => now()->subMonth()->toDateString(),
                'profile_picture' => null
            ]
        );

    }
}
