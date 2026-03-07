<?php
namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@hospital.com'], [
            'name'      => 'System Administrator',
            'password'  => Hash::make('Admin@12345'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $map = [
            'Pharmacy' => ['pharmacist', 'pharmacist@hospital.com', 'Pharmacist'],
            'Lab'      => ['lab',        'lab@hospital.com',        'Lab Technician'],
            'Theatre'  => ['theatre',    'theatre@hospital.com',    'Theatre Nurse'],
            'Ward'     => ['ward',       'ward@hospital.com',       'Ward Nurse'],
        ];

        foreach ($map as $deptName => [$role, $email, $name]) {
            $dept = Department::where('name', $deptName)->first();
            User::firstOrCreate(['email' => $email], [
                'name'          => $name,
                'password'      => Hash::make('Pass@12345'),
                'role'          => $role,
                'department_id' => $dept?->id,
                'is_active'     => true,
            ]);
        }
    }
}
