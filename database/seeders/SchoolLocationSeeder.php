<?php

namespace Database\Seeders;

use App\Models\SchoolLocation;
use Illuminate\Database\Seeder;

class SchoolLocationSeeder extends Seeder
{
    public function run(): void
    {
        SchoolLocation::updateOrCreate(
            ['name' => 'Lokasi Utama Sekolah'],
            [
                'latitude' => -6.2000000,
                'longitude' => 106.8166667,
                'radius_meters' => 100,
                'is_active' => true,
            ]
        );
    }
}
