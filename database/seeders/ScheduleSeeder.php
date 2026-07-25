<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\SchoolYear;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $schoolYear = SchoolYear::getActive();
        if (!$schoolYear) return;

        $days = [
            0 => ['is_school_day' => false, 'in' => '07:00', 'out' => '14:00'], // Minggu
            1 => ['is_school_day' => true,  'in' => '07:00', 'out' => '14:00'], // Senin
            2 => ['is_school_day' => true,  'in' => '07:00', 'out' => '14:00'], // Selasa
            3 => ['is_school_day' => true,  'in' => '07:00', 'out' => '14:00'], // Rabu
            4 => ['is_school_day' => true,  'in' => '07:00', 'out' => '14:00'], // Kamis
            5 => ['is_school_day' => true,  'in' => '07:00', 'out' => '11:30'], // Jumat
            6 => ['is_school_day' => false, 'in' => '07:00', 'out' => '14:00'], // Sabtu
        ];

        foreach ($days as $dayOfWeek => $info) {
            Schedule::updateOrCreate(
                [
                    'school_year_id' => $schoolYear->id,
                    'day_of_week' => $dayOfWeek,
                ],
                [
                    'check_in_time' => $info['in'],
                    'check_in_tolerance_minutes' => 10,
                    'check_out_time' => $info['out'],
                    'is_school_day' => $info['is_school_day'],
                ]
            );
        }
    }
}
