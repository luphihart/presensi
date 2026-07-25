<?php

namespace App\Livewire\Admin;

use App\Enums\AttendanceStatus;

use App\Enums\LeaveStatus;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\SchoolYear;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class LeaveRequestManagement extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public function approve(int $id): void
    {
        $leave = LeaveRequest::find($id);
        if (!$leave) return;

        $leave->update([
            'status' => LeaveStatus::Approved,
            'reviewed_by' => auth()->id(),
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
            'type' => \App\Enums\NotificationType::LeaveStatus,
            'title' => 'Pengajuan Izin Disetujui',
            'body' => 'Pengajuan ' . $leave->type->label() . ' Anda untuk tanggal ' . $leave->date->format('d/m/Y') . ' telah DISETUJUI oleh admin.',
            'related_type' => LeaveRequest::class,
            'related_id' => $leave->id,
        ]);
    }

    public function reject(int $id): void
    {
        $leave = LeaveRequest::find($id);
        if (!$leave) return;

        $leave->update([
            'status' => LeaveStatus::Rejected,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Notify Student
        Notification::create([
            'user_id' => $leave->student->user_id,
            'type' => \App\Enums\NotificationType::LeaveStatus,
            'title' => 'Pengajuan Izin Ditolak',
            'body' => 'Pengajuan ' . $leave->type->label() . ' Anda untuk tanggal ' . $leave->date->format('d/m/Y') . ' DITOLAK oleh admin.',
            'related_type' => LeaveRequest::class,
            'related_id' => $leave->id,
        ]);
    }

    public function deleteLeaveRequest(int $id): void
    {
        $leave = LeaveRequest::find($id);
        if ($leave) {
            $studentId = $leave->student_id;
            $dateStr = \Carbon\Carbon::parse($leave->date)->toDateString();

            // 1. Delete associated Attendance record
            Attendance::where('leave_request_id', $leave->id)
                ->orWhere(function ($q) use ($studentId, $dateStr) {
                    $q->where('student_id', $studentId)
                      ->whereDate('date', $dateStr);
                })->delete();

            // 2. Delete associated Notifications
            Notification::where('related_type', LeaveRequest::class)
                ->where('related_id', $leave->id)
                ->delete();

            // 3. Delete LeaveRequest record
            $leave->delete();
        }
    }

    public function render()
    {
        $query = LeaveRequest::with(['student.user', 'student.classRoom', 'reviewer']);

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        $requests = $query->latest()->paginate(15);

        return view('livewire.admin.leave-request-management', [
            'requests' => $requests,
        ]);
    }
}
