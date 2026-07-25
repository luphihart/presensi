<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_monthly_matrix_report(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\ReportGenerator::class)
            ->set('month', '2026-07')
            ->assertViewHas('reportData');
    }
}
