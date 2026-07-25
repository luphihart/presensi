<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SchoolLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_school_location(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\SchoolLocationManagement::class)
            ->set('name', 'Gerbang Selatan')
            ->set('latitude', '-6.210000')
            ->set('longitude', '106.820000')
            ->set('radiusMeters', 150)
            ->set('isActive', true)
            ->call('save');

        $this->assertDatabaseHas('school_locations', [
            'name' => 'Gerbang Selatan',
            'radius_meters' => 150,
        ]);
    }
}
