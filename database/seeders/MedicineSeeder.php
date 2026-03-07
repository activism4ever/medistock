<?php
namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $medicines = [
            ['name' => 'Paracetamol 500mg',   'generic_name' => 'Acetaminophen',  'dosage' => '500mg', 'unit' => 'tablets',  'category' => 'Analgesic'],
            ['name' => 'Amoxicillin 250mg',   'generic_name' => 'Amoxicillin',    'dosage' => '250mg', 'unit' => 'capsules', 'category' => 'Antibiotic'],
            ['name' => 'Metronidazole 400mg', 'generic_name' => 'Metronidazole',  'dosage' => '400mg', 'unit' => 'tablets',  'category' => 'Antibiotic'],
            ['name' => 'Omeprazole 20mg',     'generic_name' => 'Omeprazole',     'dosage' => '20mg',  'unit' => 'capsules', 'category' => 'Antacid'],
            ['name' => 'Ciprofloxacin 500mg', 'generic_name' => 'Ciprofloxacin',  'dosage' => '500mg', 'unit' => 'tablets',  'category' => 'Antibiotic'],
            ['name' => 'Diclofenac 50mg',     'generic_name' => 'Diclofenac',     'dosage' => '50mg',  'unit' => 'tablets',  'category' => 'NSAID'],
            ['name' => 'Normal Saline 1L',    'generic_name' => 'NaCl 0.9%',      'dosage' => '1L',    'unit' => 'bags',     'category' => 'IV Fluid'],
            ['name' => 'Dextrose 5% 500ml',   'generic_name' => 'Glucose 5%',     'dosage' => '500ml', 'unit' => 'bags',     'category' => 'IV Fluid'],
        ];

        foreach ($medicines as $m) {
            Medicine::firstOrCreate(['name' => $m['name']], $m);
        }
    }
}
