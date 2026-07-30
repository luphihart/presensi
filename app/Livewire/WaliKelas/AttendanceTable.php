<?php

namespace App\Livewire\WaliKelas;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.wali-kelas')]
class AttendanceTable extends Component
{
    use WithPagination;

    public string $dateFilter = '';
    public string $statusFilter = '';
    public string $search = '';

    public function mount(): void
    {
        $this->dateFilter = Carbon::today()->toDateString();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDateFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $classRoom = ClassRoom::where('wali_kelas_id', $user->id)->first();

        if (!$classRoom) {
            return view('livewire.wali-kelas.attendance-table', [
                'classRoom' => null,
                'attendances' => collect(),
            ]);
        }

        $studentIds = Student::where('class_room_id', $classRoom->id)->pluck('id');

        $query = Attendance::whereIn('student_id', $studentIds)
            ->with(['student.user']);

        if (!empty($this->dateFilter)) {
            $query->whereDate('date', $this->dateFilter);
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->whereHas('student.user', function ($uq) use ($search) {
                $uq->where('name', 'like', $search);
            });
        }

        $attendances = $query->latest('date')->paginate(15);

        return view('livewire.wali-kelas.attendance-table', [
            'classRoom' => $classRoom,
            'attendances' => $attendances,
        ]);
    }
}
