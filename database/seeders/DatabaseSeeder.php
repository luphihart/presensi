<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SchoolYearSeeder::class,
            ScheduleSeeder::class,
            SchoolLocationSeeder::class,
            SettingSeeder::class,
            AdminUserSeeder::class,
            DummyDataSeeder::class,
        ]);
    }
}
