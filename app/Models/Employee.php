<?php

namespace App\Models;

use DB;
use App\Models\User;
use App\Mail\WelcomeEmployee;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'department',
        'salary',
        'joining_date',
        'profile_picture'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function createWithUser(array $employeeData, string $role = 'Employee'): Employee
    {
        return DB::transaction(function () use ($employeeData, $role) {
            $plainPassword = $employeeData['password'] ?? 'password';

            $user = User::create([
                'name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'password' => bcrypt($employeeData['password'] ?? 'password'),
            ]);

            $user->assignRole($role);

            $employeeData['user_id'] = $user->id;
            $employee = Employee::create($employeeData);

            try {
                Mail::to($user->email)->send(new WelcomeEmployee($user, $plainPassword));
            } catch (\Exception $e) {
                \Log::error("Failed to send welcome email to {$user->email}: ".$e->getMessage());
            }

            return $employee;
        });
    }

}
