<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ClassRoom;
use App\Models\LeaveRequest;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Livewire\Livewire;
use Tests\TestCase;

class WaliKelasFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_wali_kelas()
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\WaliKelasManagement::class)
            ->set('name', 'Budi Wali Kelas')
            ->set('nip', '198501012020')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Wali Kelas',
            'nip' => '198501012020',
            'email' => '198501012020@walikelas.com',
            'role' => 'wali_kelas',
        ]);
    }

    public function test_admin_can_assign_wali_kelas_to_classroom()
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $schoolYear = SchoolYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);
        $classRoom = ClassRoom::create(['name' => '10 RPL 1', 'school_year_id' => $schoolYear->id]);

        $waliKelas = User::create([
            'name' => 'Pak Wali',
            'nip' => '1234567890',
            'email' => '1234567890@walikelas.com',
            'password' => bcrypt('walikelas123'),
            'role' => UserRole::WaliKelas,
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\ClassRoomManagement::class)
            ->call('openEdit', $classRoom->id)
            ->set('waliKelasId', $waliKelas->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('class_rooms', [
            'id' => $classRoom->id,
            'wali_kelas_id' => $waliKelas->id,
        ]);
    }

    public function test_wali_kelas_login_redirection()
    {
        $waliKelas = User::create([
            'name' => 'Pak Wali',
            'nip' => '1234567890',
            'email' => '1234567890@walikelas.com',
            'password' => bcrypt('walikelas123'),
            'role' => UserRole::WaliKelas,
            'is_active' => true,
        ]);

        Livewire::test(\App\Livewire\Auth\Login::class)
            ->set('email', '1234567890@walikelas.com')
            ->set('password', 'walikelas123')
            ->call('login')
            ->assertRedirect(route('wali_kelas.dashboard'));
    }

    public function test_wali_kelas_can_approve_leave_request_for_assigned_class()
    {
        $schoolYear = SchoolYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);
        $waliKelas = User::create([
            'name' => 'Pak Wali',
            'nip' => '1234567890',
            'email' => '1234567890@walikelas.com',
            'password' => bcrypt('walikelas123'),
            'role' => UserRole::WaliKelas,
        ]);
        $classRoom = ClassRoom::create(['name' => '10 RPL 1', 'school_year_id' => $schoolYear->id, 'wali_kelas_id' => $waliKelas->id]);

        $studentUser = User::factory()->create(['role' => UserRole::Student]);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'class_room_id' => $classRoom->id,
            'nis' => '1001',
            'birth_date' => '2008-01-01',
            'gender' => 'L',
            'enrolled_at' => '2024-07-01',
        ]);

        $leave = LeaveRequest::create([
            'student_id' => $student->id,
            'type' => \App\Enums\LeaveType::Izin,
            'date' => now()->toDateString(),
            'reason' => 'Acara keluarga',
            'status' => \App\Enums\LeaveStatus::Pending,
        ]);

        Livewire::actingAs($waliKelas)
            ->test(\App\Livewire\WaliKelas\LeaveRequests::class)
            ->call('approve', $leave->id);

        $this->assertDatabaseHas('leave_requests', [
            'id' => $leave->id,
            'status' => 'approved',
            'reviewed_by' => $waliKelas->id,
        ]);
    }

    public function test_wali_kelas_can_render_all_pages()
    {
        $schoolYear = SchoolYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);
        $waliKelas = User::create([
            'name' => 'Pak Wali',
            'nip' => '1234567890',
            'email' => '1234567890@walikelas.com',
            'password' => bcrypt('walikelas123'),
            'role' => UserRole::WaliKelas,
        ]);
        $classRoom = ClassRoom::create(['name' => '10 RPL 1', 'school_year_id' => $schoolYear->id, 'wali_kelas_id' => $waliKelas->id]);

        Livewire::actingAs($waliKelas)->test(\App\Livewire\WaliKelas\Dashboard::class)->assertStatus(200);
        Livewire::actingAs($waliKelas)->test(\App\Livewire\WaliKelas\StudentList::class)->assertStatus(200);
        Livewire::actingAs($waliKelas)->test(\App\Livewire\WaliKelas\AttendanceTable::class)->assertStatus(200);
        Livewire::actingAs($waliKelas)->test(\App\Livewire\WaliKelas\LeaveRequests::class)->assertStatus(200);
        Livewire::actingAs($waliKelas)->test(\App\Livewire\WaliKelas\Report::class)->assertStatus(200);
        Livewire::actingAs($waliKelas)->test(\App\Livewire\WaliKelas\ChangePassword::class)->assertStatus(200);
    }
}
