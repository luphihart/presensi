<?php

namespace App\Livewire\Student;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\SchoolYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class AttendanceHistory extends Component
{
    public string $currentMonth;

    public function mount(): void
    {
        $this->currentMonth = now()->format('Y-m');
    }

    public function previousMonth(): void
    {
        $this->currentMonth = Carbon::parse($this->currentMonth . '-01')->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->currentMonth = Carbon::parse($this->currentMonth . '-01')->addMonth()->format('Y-m');
    }

    public function render()
    {
        $student = Auth::user()->student;
        $date = Carbon::parse($this->currentMonth . '-01');
        $schoolYear = SchoolYear::getActive();

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $attendances = $student ? Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get()
            ->keyBy(fn($item) => $item->date->format('Y-m-d')) : collect();

        $holidays = Holiday::where('school_year_id', $schoolYear?->id)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get()
            ->keyBy(fn($item) => $item->date->format('Y-m-d'));

        // Generate grid days for the month
        $daysInMonth = $date->daysInMonth;
        $startDayOfWeek = $startOfMonth->dayOfWeek; // 0=Sunday

        return view('livewire.student.attendance-history', [
            'date' => $date,
            'daysInMonth' => $daysInMonth,
            'startDayOfWeek' => $startDayOfWeek,
            'attendances' => $attendances,
            'holidays' => $holidays,
        ]);
    }
}
