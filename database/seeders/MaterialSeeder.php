<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            'JOINT;1C;120-185mm2',
            'JOINT;1C;240-300mm2',
            'JOINT;3C;120-185mm2',
            'JOINT;3C;240-300mm2',
            'TERM;ID;1C;120-185mm2',
            'TERM;ID;1C;240-300mm2',
            'TERM;ID;3C;120-185mm2',
            'TERM;ID;3C;240-300mm2',
            'TERM;OD;1C;120-185mm2',
            'TERM;OD;1C;240-300mm2',
            'TERM;OD;3C;120-185mm2',
            'TERM;OD;3C;240-300mm2',
        ];

        foreach ($materials as $material) {
            Material::firstOrCreate([
                'material_name' => $material
            ]);
        }
    }
}
