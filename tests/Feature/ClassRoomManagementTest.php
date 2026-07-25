<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClassRoomManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_classroom_with_major(): void
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

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\ClassRoomManagement::class)
            ->set('schoolYearId', $schoolYear->id)
            ->set('name', '10 RPL 1')
            ->set('major', 'Rekayasa Perangkat Lunak')
            ->call('save');

        $this->assertDatabaseHas('class_rooms', [
            'name' => '10 RPL 1',
            'major' => 'Rekayasa Perangkat Lunak',
        ]);
    }
}
