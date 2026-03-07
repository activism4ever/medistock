<?php
namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Pharmacy', 'description' => 'Main pharmacy dispensing unit'],
            ['name' => 'Lab',      'description' => 'Laboratory department'],
            ['name' => 'Theatre',  'description' => 'Operating theatre'],
            ['name' => 'Ward',     'description' => 'General ward'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept['name']], $dept);
        }
    }
}
