<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ClassRoom;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentBulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_bulk_delete_selected_students(): void
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

        $u1 = User::create(['name' => 'Siswa 1', 'email' => 's1@test.com', 'password' => bcrypt('pass'), 'role' => UserRole::Student, 'is_active' => true]);
        $s1 = Student::create(['user_id' => $u1->id, 'class_room_id' => $classRoom->id, 'nis' => '1001', 'gender' => 'L', 'birth_date' => '2010-01-01', 'enrolled_at' => '2026-07-01', 'is_active' => true]);

        $u2 = User::create(['name' => 'Siswa 2', 'email' => 's2@test.com', 'password' => bcrypt('pass'), 'role' => UserRole::Student, 'is_active' => true]);
        $s2 = Student::create(['user_id' => $u2->id, 'class_room_id' => $classRoom->id, 'nis' => '1002', 'gender' => 'P', 'birth_date' => '2010-01-01', 'enrolled_at' => '2026-07-01', 'is_active' => true]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\StudentManagement\StudentTable::class)
            ->set('selectedStudents', [(string)$s1->id, (string)$s2->id])
            ->call('deleteSelectedStudents')
            ->assertSet('selectedStudents', []);

        $this->assertDatabaseMissing('students', ['id' => $s1->id]);
        $this->assertDatabaseMissing('students', ['id' => $s2->id]);
        $this->assertDatabaseMissing('users', ['id' => $u1->id]);
        $this->assertDatabaseMissing('users', ['id' => $u2->id]);
    }
}
