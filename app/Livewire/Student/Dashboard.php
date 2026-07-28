<?php

namespace App\Livewire\Student;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Services\BirthdayMessageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class Dashboard extends Component
{
    public bool $isBirthday = false;
    public string $birthdayGreeting = '';

    public function mount(BirthdayMessageService $birthdayService): void
    {
        $user = Auth::user();
        $student = $user->student;

        if ($student && $student->birth_date) {
            $today = Carbon::today();
            if ($student->birth_date->format('m-d') === $today->format('m-d')) {
                $this->isBirthday = true;
                $this->birthdayGreeting = $birthdayService->getMessage($student);
            }
        }
    }

    public function render()
    {
        $user = Auth::user();
        $student = $user->student;
        $today = Carbon::today();
        $schoolYear = SchoolYear::getActive();

        $todayAttendance = null;
        if ($student) {
            $todayAttendance = Attendance::where('student_id', $student->id)
                ->where('date', $today->toDateString())
                ->first();
        }

        // Fetch schedule for today
        $dayOfWeek = $today->dayOfWeek; // 0=Sunday
        $schedule = Schedule::where('school_year_id', $schoolYear?->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        // Calculate monthly attendance stats
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $monthlyAttendances = $student ? Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get() : collect();

        $totalHadir = $monthlyAttendances->whereIn('status.value', ['hadir', 'terlambat'])->count();
        $totalIzin = $monthlyAttendances->whereIn('status.value', ['izin', 'sakit'])->count();
        $totalAlpa = $monthlyAttendances->where('status.value', 'alpa')->count();
        $totalDaysRecorded = $monthlyAttendances->count();

        $attendanceRate = $totalDaysRecorded > 0 ? round(($totalHadir / $totalDaysRecorded) * 100) : 100;

        $todayHoliday = \App\Models\Holiday::where('school_year_id', $schoolYear?->id)
            ->where('date', $today->toDateString())
            ->first();

        $isSchoolDay = $schedule ? (bool)$schedule->is_school_day : true;
        if ($todayHoliday) {
            $isSchoolDay = false;
        }

        $announcements = \App\Models\Announcement::published()->latest('published_at')->take(3)->get();

        return view('livewire.student.dashboard', [
            'user' => $user,
            'student' => $student,
            'todayAttendance' => $todayAttendance,
            'schedule' => $schedule,
            'todayHoliday' => $todayHoliday,
            'isSchoolDay' => $isSchoolDay,
            'attendanceRate' => $attendanceRate,
            'totalHadir' => $totalHadir,
            'totalIzin' => $totalIzin,
            'totalAlpa' => $totalAlpa,
            'announcements' => $announcements,
        ]);
    }
}
