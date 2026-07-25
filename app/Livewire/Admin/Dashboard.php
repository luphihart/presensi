<?php

namespace App\Livewire\Admin;

use App\Enums\LeaveStatus;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Student;
use App\Models\SchoolYear;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public function approveLeave(int $leaveId): void
    {
        $leave = LeaveRequest::find($leaveId);
        if (!$leave) return;

        $leave->update([
            'status' => LeaveStatus::Approved,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $schoolYear = SchoolYear::getActive();

        // Automatically update or create attendance record with leave status
        Attendance::updateOrCreate(
            ['student_id' => $leave->student_id, 'date' => $leave->date->toDateString()],
            [
                'school_year_id' => $schoolYear?->id ?? 1,
                'status' => $leave->type->value === 'sakit' ? \App\Enums\AttendanceStatus::Sakit : \App\Enums\AttendanceStatus::Izin,
                'leave_request_id' => $leave->id,
            ]
        );
    }

    public function rejectLeave(int $leaveId): void
    {
        $leave = LeaveRequest::find($leaveId);
        if (!$leave) return;

        $leave->update([
            'status' => LeaveStatus::Rejected,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
    }

    public function render()
    {
        $today = Carbon::today()->toDateString();
        $totalStudents = Student::where('is_active', true)->count();

        $todayAttendances = Attendance::where('date', $today)->get();

        $totalHadir = $todayAttendances->where('status.value', 'hadir')->count();
        $totalTerlambat = $todayAttendances->where('status.value', 'terlambat')->count();
        $totalIzin = $todayAttendances->whereIn('status.value', ['izin', 'sakit'])->count();
        $totalAlpa = $todayAttendances->where('status.value', 'alpa')->count();

        $pendingLeaves = LeaveRequest::where('status', LeaveStatus::Pending)
            ->with(['student.user', 'student.classRoom'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'totalStudents' => $totalStudents,
            'totalHadir' => $totalHadir,
            'totalTerlambat' => $totalTerlambat,
            'totalIzin' => $totalIzin,
            'totalAlpa' => $totalAlpa,
            'pendingLeaves' => $pendingLeaves,
        ]);
    }
}
