<?php

namespace App\Console\Commands;

use App\Enums\AttendanceStatus;
use App\Enums\LeaveStatus;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateDailyAbsences extends Command
{
    protected $signature = 'attendance:calculate-absences {date?}';
    protected $description = 'Calculate daily absences (Alpa) for students without attendance or approved leave';

    public function handle(): int
    {
        $targetDate = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::today();
        $dateStr = $targetDate->toDateString();
        $dayOfWeek = $targetDate->dayOfWeek;

        $schoolYear = SchoolYear::getActive();
        if (!$schoolYear) {
            $this->warn('Tidak ada tahun ajaran aktif.');
            return Command::FAILURE;
        }

        // Skip if today is holiday
        $isHoliday = Holiday::where('school_year_id', $schoolYear->id)
            ->where('date', $dateStr)
            ->exists();

        if ($isHoliday) {
            $this->info("Hari ini ($dateStr) adalah hari libur. Perhitungan alpa dilewati.");
            return Command::SUCCESS;
        }

        // Skip if not a school day (e.g. Sunday)
        $schedule = Schedule::where('school_year_id', $schoolYear->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if ($schedule && !$schedule->is_school_day) {
            $this->info("Hari ini ($dateStr) bukan hari sekolah. Perhitungan alpa dilewati.");
            return Command::SUCCESS;
        }

        $activeStudents = Student::where('is_active', true)->get();
        $alpaCount = 0;

        foreach ($activeStudents as $student) {
            // Check existing attendance
            $hasAttendance = Attendance::where('student_id', $student->id)
                ->where('date', $dateStr)
                ->exists();

            if ($hasAttendance) continue;

            // Check approved leave request
            $hasApprovedLeave = LeaveRequest::where('student_id', $student->id)
                ->where('date', $dateStr)
                ->where('status', LeaveStatus::Approved)
                ->exists();

            if ($hasApprovedLeave) continue;

            // Mark as Alpa (Idempotent updateOrCreate)
            Attendance::updateOrCreate(
                ['student_id' => $student->id, 'date' => $dateStr],
                [
                    'school_year_id' => $schoolYear->id,
                    'status' => AttendanceStatus::Alpa,
                ]
            );

            // Send notification to student
            \App\Models\Notification::create([
                'user_id' => $student->user_id,
                'type' => \App\Enums\NotificationType::AbsenceReminder,
                'title' => 'Pemberitahuan Ketidakhadiran ⚠️',
                'body' => 'Anda ditandai Alpa (Tanpa Keterangan) untuk tanggal ' . $targetDate->locale('id')->isoFormat('D MMMM YYYY') . '.',
            ]);

            $alpaCount++;
        }

        $this->info("Perhitungan alpa selesai untuk tanggal $dateStr. Total $alpaCount murid ditandai Alpa.");
        return Command::SUCCESS;
    }
}
