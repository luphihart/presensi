<?php

namespace Tests\Feature;

use App\Enums\LeaveType;
use App\Enums\UserRole;
use App\Models\ClassRoom;
use App\Models\LeaveRequest;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeaveRequestManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_leave_request(): void
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
            'nis' => '5001',
            'gender' => 'L',
            'birth_date' => '2010-01-01',
            'enrolled_at' => '2026-07-01',
        ]);

        $leave = LeaveRequest::create([
            'student_id' => $student->id,
            'type' => LeaveType::Izin,
            'date' => '2026-07-25',
            'reason' => 'Acara Keluarga',
            'status' => \App\Enums\LeaveStatus::Pending,
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\LeaveRequestManagement::class)
            ->call('deleteLeaveRequest', $leave->id);

        $this->assertDatabaseMissing('leave_requests', [
            'id' => $leave->id,
        ]);
    }

    public function test_deleting_approved_leave_request_removes_attendance_and_notifications(): void
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
            'email' => 'student2@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Student,
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'class_room_id' => $classRoom->id,
            'nis' => '5002',
            'gender' => 'L',
            'birth_date' => '2010-01-01',
            'enrolled_at' => '2026-07-01',
        ]);

        $leave = LeaveRequest::create([
            'student_id' => $student->id,
            'type' => LeaveType::Izin,
            'date' => '2026-07-25',
            'reason' => 'Acara Keluarga',
            'status' => \App\Enums\LeaveStatus::Pending,
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\LeaveRequestManagement::class)
            ->call('approve', $leave->id);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'status' => \App\Enums\AttendanceStatus::Izin->value,
        ]);

        Livewire::test(\App\Livewire\Admin\LeaveRequestManagement::class)
            ->call('deleteLeaveRequest', $leave->id);

        $this->assertDatabaseMissing('leave_requests', ['id' => $leave->id]);
        $this->assertDatabaseMissing('attendances', ['student_id' => $student->id]);
    }
}
