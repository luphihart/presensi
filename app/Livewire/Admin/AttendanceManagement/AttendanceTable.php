<?php

namespace App\Livewire\Admin\AttendanceManagement;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\SchoolYear;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class AttendanceTable extends Component
{
    use WithPagination;

    public string $dateFilter = '';
    public string $classFilter = '';
    public string $statusFilter = '';

    // Correction Modal properties
    public bool $showCorrectionModal = false;
    public ?int $selectedAttendanceId = null;
    public string $correctionStatus = 'hadir';
    public string $correctionReason = '';

    public function mount(): void
    {
        $this->dateFilter = now()->toDateString();
    }

    public function openCorrection(int $attendanceId): void
    {
        $att = Attendance::find($attendanceId);
        if ($att) {
            $this->selectedAttendanceId = $att->id;
            $this->correctionStatus = $att->status->value;
            $this->correctionReason = '';
            $this->showCorrectionModal = true;
        }
    }

    public function saveCorrection(): void
    {
        $this->validate([
            'correctionStatus' => 'required|in:hadir,terlambat,izin,sakit,alpa',
            'correctionReason' => 'required|string|min:5|max:500',
        ], [
            'correctionReason.required' => 'Alasan koreksi wajib diisi untuk pencatatan audit log.',
        ]);

        $att = Attendance::find($this->selectedAttendanceId);
        if ($att) {
            $att->update([
                'status' => AttendanceStatus::from($this->correctionStatus),
                'is_manual_correction' => true,
                'corrected_by' => auth()->id(),
                'correction_reason' => $this->correctionReason,
            ]);
        }

        $this->showCorrectionModal = false;
        $this->reset(['selectedAttendanceId', 'correctionReason']);
    }

    public function deleteAttendance(int $attendanceId): void
    {
        $att = Attendance::find($attendanceId);
        if ($att) {
            $att->delete();
        }
    }

    public function render()
    {
        $schoolYear = SchoolYear::getActive();
        $classRooms = $schoolYear ? ClassRoom::where('school_year_id', $schoolYear->id)->get() : collect();

        $query = Attendance::with(['student.user', 'student.classRoom', 'corrector']);

        if (!empty($this->dateFilter)) {
            $query->where('date', $this->dateFilter);
        }

        if (!empty($this->classFilter)) {
            $query->whereHas('student', function ($q) {
                $q->where('class_room_id', $this->classFilter);
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        $attendances = $query->latest('date')->paginate(15);

        return view('livewire.admin.attendance-management.attendance-table', [
            'attendances' => $attendances,
            'classRooms' => $classRooms,
        ]);
    }
}
