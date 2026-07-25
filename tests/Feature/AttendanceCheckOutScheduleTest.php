<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AttendanceCheckOutScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocks_checkout_before_scheduled_checkout_time(): void
    {
        // Fix time at 11:00 AM on Monday
        Carbon::setTestNow(Carbon::parse('2026-07-27 11:00:00'));

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

        // Schedule: Monday (day 1), Check out time is 14:00
        Schedule::create([
            'school_year_id' => $schoolYear->id,
            'day_of_week' => 1,
            'check_in_time' => '07:00:00',
            'check_in_tolerance_minutes' => 15,
            'check_out_time' => '14:00:00',
            'is_school_day' => true,
        ]);

        $user = User::create([
            'name' => 'Murid Test',
            'email' => 'murid@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Student,
            'is_active' => true,
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'class_room_id' => $classRoom->id,
            'nis' => '99001',
            'gender' => 'L',
            'birth_date' => '2010-01-01',
            'enrolled_at' => '2026-07-01',
            'is_active' => true,
        ]);

        // Student already checked in at 06:55
        Attendance::create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'date' => '2026-07-27',
            'check_in_at' => '2026-07-27 06:55:00',
            'status' => \App\Enums\AttendanceStatus::Hadir,
        ]);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\Student\AttendanceCheckOut::class)
            ->assertSet('errorMessage', 'Presensi pulang belum dibuka. Jam pulang sekolah hari ini adalah pukul 14:00 WIB.')
            ->call('submitCheckOut')
            ->assertSet('errorMessage', 'Presensi pulang belum dibuka. Jam pulang sekolah hari ini adalah pukul 14:00 WIB.');

        Carbon::setTestNow();
    }
}
