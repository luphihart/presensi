<?php

namespace Tests\Feature;

use App\Enums\AnnouncementStatus;
use App\Enums\UserRole;
use App\Models\Announcement;
use App\Models\ClassRoom;
use App\Models\Notification;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AnnouncementManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_announcement_as_draft(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\AnnouncementManagement::class)
            ->set('title', 'Libur Hari Raya')
            ->set('content', 'Sekolah libur mulai tanggal 10.')
            ->call('save', 'draft')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('announcements', [
            'title' => 'Libur Hari Raya',
            'status' => AnnouncementStatus::Draft->value,
        ]);
    }

    public function test_publishing_announcement_sends_notification_to_all_active_students(): void
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

        $studentUser = User::factory()->create(['role' => UserRole::Student]);
        Student::create([
            'user_id' => $studentUser->id,
            'class_room_id' => $classRoom->id,
            'nis' => '1001',
            'birth_date' => '2008-01-01',
            'gender' => 'L',
            'enrolled_at' => '2026-07-01',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\AnnouncementManagement::class)
            ->set('title', 'Pengumuman Penting')
            ->set('content', 'Isi pengumuman penting.')
            ->call('save', 'published')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('announcements', [
            'title' => 'Pengumuman Penting',
            'status' => AnnouncementStatus::Published->value,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $studentUser->id,
            'title' => '📢 Pengumuman Baru',
        ]);
    }

    public function test_draft_announcement_not_shown_to_students(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        Announcement::create([
            'title' => 'Draft Pengumuman',
            'content' => 'Isi draft',
            'created_by' => $admin->id,
            'status' => AnnouncementStatus::Draft,
        ]);

        $studentUser = User::factory()->create(['role' => UserRole::Student]);

        Livewire::actingAs($studentUser)
            ->test(\App\Livewire\Student\AnnouncementList::class)
            ->assertDontSee('Draft Pengumuman');
    }

    public function test_published_announcement_shown_in_student_announcement_list(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        Announcement::create([
            'title' => 'Pengumuman Ujian',
            'content' => 'Ujian dimulai minggu depan.',
            'created_by' => $admin->id,
            'status' => AnnouncementStatus::Published,
            'published_at' => now(),
        ]);

        $studentUser = User::factory()->create(['role' => UserRole::Student]);

        Livewire::actingAs($studentUser)
            ->test(\App\Livewire\Student\AnnouncementList::class)
            ->assertSee('Pengumuman Ujian');
    }
}
