<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Models\ClassRoom;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateDailyAbsencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_marks_active_students_without_attendance_as_alpa(): void
    {
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
            'name' => 'Budi',
            'email' => 'budi@test.com',
            'password' => bcrypt('password'),
            'role' => \App\Enums\UserRole::Student,
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'class_room_id' => $classRoom->id,
            'nis' => '1001',
            'gender' => 'L',
            'birth_date' => '2010-01-01',
            'enrolled_at' => '2026-07-01',
            'is_active' => true,
        ]);

        // Run command for a Monday date
        $this->artisan('attendance:calculate-absences 2026-07-27')
            ->assertExitCode(0);

        $attendance = \App\Models\Attendance::where('student_id', $student->id)->first();
        $this->assertNotNull($attendance);
        $this->assertEquals(AttendanceStatus::Alpa, $attendance->status);
    }
}
