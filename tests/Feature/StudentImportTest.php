<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ClassRoom;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\Services\StudentImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_student_import_template(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\StudentManagement\StudentTable::class)
            ->call('downloadTemplate')
            ->assertFileDownloaded('template-upload-murid.xlsx');
    }

    public function test_student_import_service_parses_template_rows(): void
    {
        SchoolYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        $service = new StudentImportService();

        $rows = [
            ['TEMPLATE UPLOAD DATA MURID PRESENSI'],
            ['Petunjuk: Hapus baris contoh sebelum mengunggah...'],
            [],
            ['NIS *', 'Nama Lengkap *', 'Email *', 'Password *', 'Kelas *', 'Jenis Kelamin (L/P) *', 'Tanggal Lahir (YYYY-MM-DD) *', 'No Telepon', 'Alamat'],
            ['2026099', 'Siswa Baru Test', 'siswabaru@test.com', 'password123', '7A', 'L', '2010-06-01', '081234567890', 'Jl. Merdeka No. 1'],
        ];

        $result = $service->import($rows);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['imported_count']);
        $this->assertDatabaseHas('students', ['nis' => '2026099']);
        $this->assertDatabaseHas('users', ['email' => 'siswabaru@test.com']);
    }

    public function test_student_import_service_restores_soft_deleted_students(): void
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
            'name' => 'Lama Test',
            'email' => 'lama@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Student,
            'is_active' => true,
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'class_room_id' => $classRoom->id,
            'nis' => '18840',
            'gender' => 'P',
            'birth_date' => '2010-10-28',
            'enrolled_at' => '2026-07-01',
            'is_active' => true,
        ]);

        // Soft delete both
        $user->delete();
        $student->delete();

        $this->assertTrue($student->trashed());
        $this->assertTrue($user->trashed());

        $service = new StudentImportService();
        $rows = [
            ['NIS *', 'Nama Lengkap *', 'Email *', 'Password *', 'Kelas *', 'Jenis Kelamin (L/P) *', 'Tanggal Lahir (YYYY-MM-DD) *'],
            ['18840', 'Lama Test Restore', 'lama@test.com', 'password123', '7A', 'P', '2010-10-28'],
        ];

        $result = $service->import($rows);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['imported_count']);
        $this->assertDatabaseHas('students', ['nis' => '18840', 'deleted_at' => null]);
        $this->assertDatabaseHas('users', ['email' => 'lama@test.com', 'deleted_at' => null]);
    }
}
