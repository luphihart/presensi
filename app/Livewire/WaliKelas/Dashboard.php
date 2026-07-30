<?php

namespace App\Livewire\WaliKelas;

use App\Enums\AttendanceStatus;
use App\Enums\LeaveStatus;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\LeaveRequest;
use App\Models\Student;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.wali-kelas')]
class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $classRoom = ClassRoom::where('wali_kelas_id', $user->id)->withCount('students')->first();

        if (!$classRoom) {
            return view('livewire.wali-kelas.dashboard', [
                'classRoom' => null,
            ]);
        }

        $today = Carbon::today()->toDateString();
        $studentsCount = $classRoom->students_count;

        // Get student IDs in this classroom
        $studentIds = Student::where('class_room_id', $classRoom->id)->pluck('id');

        // Today attendances
        $attendancesToday = Attendance::whereIn('student_id', $studentIds)
            ->whereDate('date', $today)
            ->get();

        $hadirCount = $attendancesToday->whereIn('status', [AttendanceStatus::Hadir, AttendanceStatus::Terlambat])->count();
        $izinSakitCount = $attendancesToday->whereIn('status', [AttendanceStatus::Izin, AttendanceStatus::Sakit])->count();
        $alpaCount = $attendancesToday->where('status', AttendanceStatus::Alpa)->count();
        $belumPresensiCount = max(0, $studentsCount - $attendancesToday->count());

        // Pending leave requests for this class
        $pendingLeaves = LeaveRequest::whereIn('student_id', $studentIds)
            ->where('status', LeaveStatus::Pending)
            ->with('student.user')
            ->latest()
            ->get();

        // Recent absences today
        $absentToday = $attendancesToday->whereIn('status', [AttendanceStatus::Izin, AttendanceStatus::Sakit, AttendanceStatus::Alpa]);

        return view('livewire.wali-kelas.dashboard', [
            'classRoom' => $classRoom,
            'studentsCount' => $studentsCount,
            'hadirCount' => $hadirCount,
            'izinSakitCount' => $izinSakitCount,
            'alpaCount' => $alpaCount,
            'belumPresensiCount' => $belumPresensiCount,
            'pendingLeaves' => $pendingLeaves,
            'absentToday' => $absentToday,
            'todayDate' => Carbon::today()->isoFormat('dddd, D MMMM YYYY'),
        ]);
    }
}
