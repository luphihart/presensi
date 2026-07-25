<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\ClassRoom;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $schoolYear = SchoolYear::getActive();
        if (!$schoolYear) return;

        $class7A = ClassRoom::firstOrCreate([
            'school_year_id' => $schoolYear->id,
            'name' => '7A',
        ]);

        $class7B = ClassRoom::firstOrCreate([
            'school_year_id' => $schoolYear->id,
            'name' => '7B',
        ]);

        $dummyStudents = [
            [
                'name' => 'Andi Pratama',
                'email' => 'andi@sekolah.sch.id',
                'nis' => '2026001',
                'gender' => 'L',
                'class' => $class7A,
                'birth_date' => '2012-07-25', // Today is birthday for testing!
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@sekolah.sch.id',
                'nis' => '2026002',
                'gender' => 'P',
                'class' => $class7A,
                'birth_date' => '2012-05-15',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@sekolah.sch.id',
                'nis' => '2026003',
                'gender' => 'L',
                'class' => $class7B,
                'birth_date' => '2012-09-10',
            ],
        ];

        foreach ($dummyStudents as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::Student,
                    'is_active' => true,
                ]
            );

            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'class_room_id' => $data['class']->id,
                    'nis' => $data['nis'],
                    'gender' => $data['gender'],
                    'birth_date' => $data['birth_date'],
                    'enrolled_at' => '2026-07-01',
                    'is_active' => true,
                ]
            );
        }
    }
}
