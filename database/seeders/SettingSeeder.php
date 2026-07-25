<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('school_name', 'SMA Negeri 1 Nusantara', 'Nama resmi sekolah');
        Setting::set('default_radius_meters', '100', 'Radius geofence default dalam meter');
        Setting::set('default_tolerance_minutes', '10', 'Toleransi keterlambatan presensi masuk dalam menit');
    }
}
