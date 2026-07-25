<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ClassRoom;
use App\Models\Holiday;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SchoolDayAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocks_check_in_on_holiday(): void
    {
        $today = Carbon::today();

        $schoolYear = SchoolYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        $classRoom = ClassRoom::create([
            'school_year_id' => $schoolYear->id,
            'name' => '7A',
        ]);

        $user = User::create([
            'name' => 'Murid Test',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Student,
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'class_room_id' => $classRoom->id,
            'nis' => '4001',
            'gender' => 'L',
            'birth_date' => '2010-01-01',
            'enrolled_at' => '2026-07-01',
        ]);

        // Mark today as holiday
        Holiday::create([
            'school_year_id' => $schoolYear->id,
            'date' => $today->toDateString(),
            'name' => 'Hari Libur Nasional',
            'type' => 'national',
        ]);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\Student\AttendanceCheckIn::class)
            ->assertSet('errorMessage', 'Hari ini (Hari Libur Nasional) adalah hari libur. Presensi tidak dapat dilakukan.');
    }
}
