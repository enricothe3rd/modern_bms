<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sector;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = [
            ['id' => 1, 'name' => 'GENERAL PUBLIC SERVICES'],
            ['id' => 2, 'name' => 'SOCIAL SERVICES'],
            ['id' => 3, 'name' => 'ECONOMIC SERVICES'],
            ['id' => 4, 'name' => 'OTHER SERVICES'],
        ];

        foreach ($sectors as $sector) {
            Sector::updateOrCreate(['id' => $sector['id']], $sector);
        }
    }
}
