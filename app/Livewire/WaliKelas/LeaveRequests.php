<?php

namespace App\Livewire\WaliKelas;

use App\Enums\AttendanceStatus;
use App\Enums\LeaveStatus;
use App\Enums\NotificationType;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\SchoolYear;
use App\Models\Student;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.wali-kelas')]
class LeaveRequests extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function approve(int $id): void
    {
        $user = auth()->user();
        $classRoom = ClassRoom::where('wali_kelas_id', $user->id)->first();
        if (!$classRoom) return;

        $leave = LeaveRequest::whereHas('student', function ($q) use ($classRoom) {
            $q->where('class_room_id', $classRoom->id);
        })->find($id);

        if (!$leave) return;

        $leave->update([
            'status' => LeaveStatus::Approved,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        $schoolYear = SchoolYear::getActive();

        // Update or create attendance
        Attendance::updateOrCreate(
            ['student_id' => $leave->student_id, 'date' => $leave->date->toDateString()],
            [
                'school_year_id' => $schoolYear?->id ?? 1,
                'status' => $leave->type->value === 'sakit' ? AttendanceStatus::Sakit : AttendanceStatus::Izin,
                'leave_request_id' => $leave->id,
            ]
        );

        // Notify Student
        Notification::create([
            'user_id' => $leave->student->user_id,
            'type' => NotificationType::LeaveStatus,
            'title' => 'Pengajuan Izin Disetujui',
            'body' => 'Pengajuan ' . $leave->type->label() . ' Anda untuk tanggal ' . $leave->date->isoFormat('D MMMM YYYY') . ' telah DISETUJUI oleh Wali Kelas (' . $user->name . ').',
            'related_type' => LeaveRequest::class,
            'related_id' => $leave->id,
        ]);

        app(\App\Services\AttendanceStreakService::class)->recalculateStreak($leave->student);
    }

    public function reject(int $id): void
    {
        $user = auth()->user();
        $classRoom = ClassRoom::where('wali_kelas_id', $user->id)->first();
        if (!$classRoom) return;

        $leave = LeaveRequest::whereHas('student', function ($q) use ($classRoom) {
            $q->where('class_room_id', $classRoom->id);
        })->find($id);

        if (!$leave) return;

        $leave->update([
            'status' => LeaveStatus::Rejected,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        // Notify Student
        Notification::create([
            'user_id' => $leave->student->user_id,
            'type' => NotificationType::LeaveStatus,
            'title' => 'Pengajuan Izin Ditolak',
            'body' => 'Pengajuan ' . $leave->type->label() . ' Anda untuk tanggal ' . $leave->date->isoFormat('D MMMM YYYY') . ' DITOLAK oleh Wali Kelas (' . $user->name . ').',
            'related_type' => LeaveRequest::class,
            'related_id' => $leave->id,
        ]);

        app(\App\Services\AttendanceStreakService::class)->recalculateStreak($leave->student);
    }

    public function render()
    {
        $user = auth()->user();
        $classRoom = ClassRoom::where('wali_kelas_id', $user->id)->first();

        if (!$classRoom) {
            return view('livewire.wali-kelas.leave-requests', [
                'classRoom' => null,
                'requests' => collect(),
            ]);
        }

        $studentIds = Student::where('class_room_id', $classRoom->id)->pluck('id');

        $query = LeaveRequest::whereIn('student_id', $studentIds)
            ->with(['student.user', 'reviewer']);

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        $requests = $query->latest()->paginate(15);

        return view('livewire.wali-kelas.leave-requests', [
            'classRoom' => $classRoom,
            'requests' => $requests,
        ]);
    }
}
