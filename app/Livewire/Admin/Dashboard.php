<?php

namespace App\Livewire\Admin;

use App\Enums\LeaveStatus;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\SchoolLocation;
use App\Models\SchoolYear;
use App\Models\Student;
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

        $statusCounts = Attendance::where('date', $today)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $totalHadir = $statusCounts['hadir'] ?? 0;
        $totalTerlambat = $statusCounts['terlambat'] ?? 0;
        $totalIzin = ($statusCounts['izin'] ?? 0) + ($statusCounts['sakit'] ?? 0);
        $totalAlpa = $statusCounts['alpa'] ?? 0;

        $pendingLeaves = LeaveRequest::where('status', LeaveStatus::Pending)
            ->with(['student.user', 'student.classRoom'])
            ->latest()
            ->take(5)
            ->get();

        $schoolLocation = \Illuminate\Support\Facades\Cache::remember('school_location_first', 3600, fn() => SchoolLocation::first());

        $mapData = Attendance::where('date', $today)
            ->whereNotNull('check_in_latitude')
            ->whereNotNull('check_in_longitude')
            ->with(['student.user', 'student.classRoom'])
            ->get()
            ->map(fn($att) => [
                'id' => $att->id,
                'name' => $att->student?->user?->name ?? 'Murid',
                'class' => $att->student?->classRoom?->name ?? '-',
                'nis' => $att->student?->nis ?? '-',
                'status' => $att->status?->label() ?? 'Hadir',
                'status_val' => $att->status ? strtolower($att->status->value) : 'hadir',
                'time' => $att->check_in_at ? $att->check_in_at->format('H:i') . ' WIB' : '-',
                'distance' => $att->check_in_distance_meters !== null ? round($att->check_in_distance_meters) : 0,
                'lat' => (float)$att->check_in_latitude,
                'lng' => (float)$att->check_in_longitude,
                'photo' => $att->check_in_photo_path ? asset('storage/' . $att->check_in_photo_path) : null,
            ])
            ->values()
            ->toArray();

        $mapConfigJson = json_encode([
            'schoolLat'    => $schoolLocation?->latitude ?? -6.200000,
            'schoolLng'    => $schoolLocation?->longitude ?? 106.816666,
            'schoolRadius' => $schoolLocation?->radius_meters ?? 100,
            'schoolName'   => $schoolLocation?->name ?? 'Sekolah',
            'students'     => $mapData,
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);

        $topDisciplineStudents = Student::with(['user', 'classRoom'])
            ->where('is_active', true)
            ->orderByDesc('monthly_points')
            ->orderByDesc('total_points')
            ->orderByDesc('current_streak')
            ->orderByRaw('CASE WHEN avg_check_in_seconds IS NULL THEN 1 ELSE 0 END, avg_check_in_seconds ASC')
            ->take(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'totalStudents' => $totalStudents,
            'totalHadir'    => $totalHadir,
            'totalTerlambat'=> $totalTerlambat,
            'totalIzin'     => $totalIzin,
            'totalAlpa'     => $totalAlpa,
            'pendingLeaves' => $pendingLeaves,
            'schoolLocation'=> $schoolLocation,
            'mapData'       => $mapData,
            'mapConfigJson' => $mapConfigJson,
            'topDisciplineStudents' => $topDisciplineStudents,
        ]);
    }
}
