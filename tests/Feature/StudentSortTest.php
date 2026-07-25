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

class StudentSortTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_sort_students_by_name(): void
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

        $u1 = User::create(['name' => 'Zulham', 'email' => 'zulham@test.com', 'password' => bcrypt('pass'), 'role' => UserRole::Student, 'is_active' => true]);
        Student::create(['user_id' => $u1->id, 'class_room_id' => $classRoom->id, 'nis' => '1001', 'gender' => 'L', 'birth_date' => '2010-01-01', 'enrolled_at' => '2026-07-01', 'is_active' => true]);

        $u2 = User::create(['name' => 'Adit', 'email' => 'adit@test.com', 'password' => bcrypt('pass'), 'role' => UserRole::Student, 'is_active' => true]);
        Student::create(['user_id' => $u2->id, 'class_room_id' => $classRoom->id, 'nis' => '1002', 'gender' => 'L', 'birth_date' => '2010-01-01', 'enrolled_at' => '2026-07-01', 'is_active' => true]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\StudentManagement\StudentTable::class)
            ->assertSet('sortColumn', 'name')
            ->assertSet('sortDirection', 'asc')
            ->call('sortBy', 'name')
            ->assertSet('sortDirection', 'desc');
    }
}
