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

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_change_password_with_correct_current_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role' => UserRole::Student,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Student\ChangePassword::class)
            ->set('current_password', 'password123')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('changePassword')
            ->assertHasNoErrors()
            ->assertSet('successMessage', 'Password berhasil diperbarui.');

        $this->assertTrue(auth()->validate([
            'email' => $user->email,
            'password' => 'newpassword123',
        ]));
    }

    public function test_student_cannot_change_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role' => UserRole::Student,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Student\ChangePassword::class)
            ->set('current_password', 'wrongpassword')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('changePassword')
            ->assertHasErrors(['current_password']);
    }

    public function test_admin_can_reset_single_student_password(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $schoolYear = SchoolYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        $classRoom = ClassRoom::create([
            'school_year_id' => $schoolYear->id,
            'name' => 'X-A',
        ]);

        $studentUser = User::factory()->create([
            'name' => 'Murid Budi',
            'role' => UserRole::Student,
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'class_room_id' => $classRoom->id,
            'nis' => '1001',
            'birth_date' => '2008-01-01',
            'gender' => 'L',
            'enrolled_at' => '2026-07-01',
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\StudentManagement\StudentTable::class)
            ->call('resetPassword', $student->id)
            ->assertSet('showResetResultModal', true);

        $studentUser->refresh();
        $this->assertFalse(auth()->validate([
            'email' => $studentUser->email,
            'password' => 'oldpassword',
        ]));
    }

    public function test_admin_can_bulk_reset_student_passwords(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $schoolYear = SchoolYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        $classRoom = ClassRoom::create([
            'school_year_id' => $schoolYear->id,
            'name' => 'X-A',
        ]);

        $studentUser1 = User::factory()->create(['role' => UserRole::Student]);
        $student1 = Student::create([
            'user_id' => $studentUser1->id,
            'class_room_id' => $classRoom->id,
            'nis' => '1001',
            'birth_date' => '2008-01-01',
            'gender' => 'L',
            'enrolled_at' => '2026-07-01',
        ]);

        $studentUser2 = User::factory()->create(['role' => UserRole::Student]);
        $student2 = Student::create([
            'user_id' => $studentUser2->id,
            'class_room_id' => $classRoom->id,
            'nis' => '1002',
            'birth_date' => '2008-01-01',
            'gender' => 'P',
            'enrolled_at' => '2026-07-01',
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\StudentManagement\StudentTable::class)
            ->set('selectedStudents', [(string)$student1->id, (string)$student2->id])
            ->call('bulkResetPassword')
            ->assertSet('showResetResultModal', true);
    }
}
