<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AttendanceTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_attendance_record(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

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

        $studentUser = User::create([
            'name' => 'Student Test',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Student,
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'class_room_id' => $classRoom->id,
            'nis' => '3001',
            'gender' => 'L',
            'birth_date' => '2010-01-01',
            'enrolled_at' => '2026-07-01',
        ]);

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'date' => '2026-07-25',
            'status' => AttendanceStatus::Hadir,
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\AttendanceManagement\AttendanceTable::class)
            ->call('deleteAttendance', $attendance->id);

        $this->assertDatabaseMissing('attendances', [
            'id' => $attendance->id,
        ]);
    }
}
