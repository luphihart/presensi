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

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_login_successfully(): void
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
            'name' => 'Andi',
            'email' => 'andi@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Student,
            'is_active' => true,
        ]);

        Student::create([
            'user_id' => $user->id,
            'class_room_id' => $classRoom->id,
            'nis' => '2026001',
            'gender' => 'L',
            'birth_date' => '2012-07-25',
            'enrolled_at' => '2026-07-01',
            'is_active' => true,
        ]);

        Livewire::test(\App\Livewire\Auth\Login::class)
            ->set('email', 'andi@test.com')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($user);
    }
}
