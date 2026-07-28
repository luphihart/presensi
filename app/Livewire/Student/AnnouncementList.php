<?php

namespace App\Livewire\Student;

use App\Models\Announcement;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.student')]
class AnnouncementList extends Component
{
    use WithPagination;

    public function render()
    {
        $announcements = Announcement::published()->latest('published_at')->paginate(10);

        return view('livewire.student.announcement-list', [
            'announcements' => $announcements,
        ]);
    }
}
