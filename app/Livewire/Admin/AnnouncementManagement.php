<?php

namespace App\Livewire\Admin;

use App\Enums\AnnouncementStatus;
use App\Enums\NotificationType;
use App\Models\Announcement;
use App\Models\Notification;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class AnnouncementManagement extends Component
{
    use WithPagination;

    public bool $showFormModal = false;
    public ?int $announcementId = null;

    #[Validate('required|string|max:200')]
    public string $title = '';

    #[Validate('required|string')]
    public string $content = '';

    public ?string $successMessage = null;

    public function openCreate(): void
    {
        $this->reset(['announcementId', 'title', 'content']);
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $announcement = Announcement::find($id);
        if ($announcement) {
            $this->announcementId = $announcement->id;
            $this->title = $announcement->title;
            $this->content = $announcement->content;
            $this->showFormModal = true;
        }
    }

    public function save(string $targetStatus = 'draft'): void
    {
        $this->validate();

        $status = $targetStatus === 'published' ? AnnouncementStatus::Published : AnnouncementStatus::Draft;
        $now = now();

        if ($this->announcementId) {
            $announcement = Announcement::find($this->announcementId);
            if ($announcement) {
                $wasPublished = $announcement->status === AnnouncementStatus::Published;
                
                $announcement->update([
                    'title' => $this->title,
                    'content' => $this->content,
                    'status' => $status,
                    'published_at' => ($status === AnnouncementStatus::Published && !$wasPublished) ? $now : $announcement->published_at,
                ]);

                // Send notification if newly published
                if ($status === AnnouncementStatus::Published && !$wasPublished) {
                    $this->notifyStudents($announcement);
                }

                $this->successMessage = 'Pengumuman berhasil diperbarui.';
            }
        } else {
            $announcement = Announcement::create([
                'title' => $this->title,
                'content' => $this->content,
                'created_by' => Auth::id(),
                'status' => $status,
                'published_at' => $status === AnnouncementStatus::Published ? $now : null,
            ]);

            if ($status === AnnouncementStatus::Published) {
                $this->notifyStudents($announcement);
            }

            $this->successMessage = 'Pengumuman baru berhasil dibuat.';
        }

        $this->showFormModal = false;
        $this->reset(['announcementId', 'title', 'content']);
    }

    public function toggleStatus(int $id): void
    {
        $announcement = Announcement::find($id);
        if ($announcement) {
            if ($announcement->status === AnnouncementStatus::Draft) {
                $announcement->update([
                    'status' => AnnouncementStatus::Published,
                    'published_at' => $announcement->published_at ?? now(),
                ]);
                $this->notifyStudents($announcement);
                $this->successMessage = 'Pengumuman telah dipublish.';
            } else {
                $announcement->update([
                    'status' => AnnouncementStatus::Draft,
                ]);
                $this->successMessage = 'Pengumuman diubah menjadi Draft.';
            }
        }
    }

    public function deleteAnnouncement(int $id): void
    {
        $announcement = Announcement::find($id);
        if ($announcement) {
            $announcement->delete();
            $this->successMessage = 'Pengumuman berhasil dihapus.';
        }
    }

    protected function notifyStudents(Announcement $announcement): void
    {
        $students = Student::where('is_active', true)->with('user')->get();
        foreach ($students as $student) {
            if ($student->user) {
                Notification::create([
                    'user_id' => $student->user_id,
                    'type' => NotificationType::Announcement,
                    'title' => '📢 Pengumuman Baru',
                    'body' => $announcement->title,
                    'related_type' => Announcement::class,
                    'related_id' => $announcement->id,
                ]);
            }
        }
    }

    public function render()
    {
        $announcements = Announcement::with('createdBy')->latest()->paginate(10);

        return view('livewire.admin.announcement-management', [
            'announcements' => $announcements,
        ]);
    }
}
