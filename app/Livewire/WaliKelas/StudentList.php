<?php

namespace App\Livewire\WaliKelas;

use App\Models\ClassRoom;
use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.wali-kelas')]
class StudentList extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $classRoom = ClassRoom::where('wali_kelas_id', $user->id)->first();

        if (!$classRoom) {
            return view('livewire.wali-kelas.student-list', [
                'classRoom' => null,
                'students' => collect(),
            ]);
        }

        $query = Student::where('class_room_id', $classRoom->id)
            ->with(['user']);

        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', $search)
                       ->orWhere('email', 'like', $search);
                })->orWhere('nis', 'like', $search);
            });
        }

        $students = $query->orderBy('nis')->paginate(15);

        return view('livewire.wali-kelas.student-list', [
            'classRoom' => $classRoom,
            'students' => $students,
        ]);
    }
}
