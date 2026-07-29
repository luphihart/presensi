<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\DisciplinePoint;
use App\Models\Notification;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentBadge;
use Carbon\Carbon;

class DisciplinePointService
{
    public const BADGES_DEFINITION = [
        'first_checkin' => [
            'name' => 'Langkah Pertama',
            'icon' => '👟',
            'description' => 'Melakukan presensi masuk untuk pertama kalinya.',
            'category' => 'milestone',
        ],
        'hadir_10' => [
            'name' => 'Setia Hadir',
            'icon' => '🌱',
            'description' => 'Mencapai total 10 hari kehadiran.',
            'category' => 'attendance',
        ],
        'hadir_50' => [
            'name' => 'Setengah Ratus',
            'icon' => '🎯',
            'description' => 'Mencapai total 50 hari kehadiran.',
            'category' => 'attendance',
        ],
        'hadir_100' => [
            'name' => 'Centurion',
            'icon' => '🦅',
            'description' => 'Mencapai total 100 hari kehadiran.',
            'category' => 'attendance',
        ],
        'perfect_week' => [
            'name' => 'Minggu Sempurna',
            'icon' => '⭐',
            'description' => 'Hadir 5 hari berturut-turut tanpa pernah terlambat dalam 1 minggu.',
            'category' => 'special',
        ],
        'perfect_month' => [
            'name' => 'Bulan Sempurna',
            'icon' => '🌙',
            'description' => 'Tercatat hadir di seluruh hari sekolah dalam 1 bulan.',
            'category' => 'special',
        ],
        'top_class' => [
            'name' => 'Juara Kelas',
            'icon' => '🏆',
            'description' => 'Menempati posisi peringkat #1 di leaderboard kelas.',
            'category' => 'rank',
        ],
        'comeback' => [
            'name' => 'Comeback Kid',
            'icon' => '💪',
            'description' => 'Bangkit setelah pernah alpa dan berhasil streak hadir 10 hari.',
            'category' => 'special',
        ],
    ];

    /**
     * Calculate and sync points for a student's attendance record.
     */
    public function syncAttendancePoints(Student $student, Attendance $attendance, bool $deferTotals = false): void
    {
        $statusVal = is_string($attendance->status) ? $attendance->status : $attendance->status->value;
        $hasCheckOut = !is_null($attendance->check_out_at);

        $points = 0;
        $reason = '';

        switch ($statusVal) {
            case AttendanceStatus::Hadir->value:
                if ($hasCheckOut) {
                    $points = 10;
                    $reason = 'Hadir Tepat Waktu (Lengkap Masuk & Pulang)';
                } else {
                    $points = 7;
                    $reason = 'Hadir Tepat Waktu (Presensi Masuk)';
                }
                break;
            case AttendanceStatus::Terlambat->value:
                if ($hasCheckOut) {
                    $points = 5;
                    $reason = 'Hadir Terlambat (Lengkap Masuk & Pulang)';
                } else {
                    $points = 3;
                    $reason = 'Hadir Terlambat (Presensi Masuk)';
                }
                break;
            case AttendanceStatus::Izin->value:
                $points = 3;
                $reason = 'Izin (Disetujui)';
                break;
            case AttendanceStatus::Sakit->value:
                $points = 3;
                $reason = 'Sakit (Disetujui)';
                break;
            default:
                $points = 0;
                $reason = 'Alpa / Tidak Hadir';
                break;
        }

        $dateStr = $attendance->date instanceof Carbon ? $attendance->date->toDateString() : (string)$attendance->date;

        if ($points > 0) {
            DisciplinePoint::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'attendance_id' => $attendance->id,
                ],
                [
                    'school_year_id' => $attendance->school_year_id,
                    'date' => $dateStr,
                    'points' => $points,
                    'reason' => $reason,
                ]
            );
        } else {
            DisciplinePoint::where('student_id', $student->id)
                ->where('attendance_id', $attendance->id)
                ->delete();
        }

        if (!$deferTotals) {
            $this->recalculateStudentTotals($student);
            $this->checkAndAwardBadges($student);
        }
    }

    /**
     * Recalculate total_points and monthly_points for a student based on discipline_points.
     */
    public function recalculateStudentTotals(Student $student): void
    {
        $totalPoints = (int) DisciplinePoint::where('student_id', $student->id)->sum('points');

        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth()->toDateString();
        $endOfMonth = $today->copy()->endOfMonth()->toDateString();

        $monthlyPoints = (int) DisciplinePoint::where('student_id', $student->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('points');

        // Calculate average check-in time of day in seconds past midnight for current month (earliest check-in tie-breaker)
        $avgCheckInSeconds = null;
        $checkIns = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('check_in_at')
            ->get();

        if ($checkIns->isNotEmpty()) {
            $totalSecs = 0;
            foreach ($checkIns as $att) {
                $time = Carbon::parse($att->check_in_at);
                $totalSecs += ($time->hour * 3600 + $time->minute * 60 + $time->second);
            }
            $avgCheckInSeconds = (int) round($totalSecs / $checkIns->count());
        }

        $student->update([
            'total_points' => $totalPoints,
            'monthly_points' => $monthlyPoints,
            'avg_check_in_seconds' => $avgCheckInSeconds,
        ]);

        $this->clearLeaderboardCache($student->class_room_id);
    }

    /**
     * Clear cached leaderboard data.
     */
    public function clearLeaderboardCache(?int $classRoomId = null): void
    {
        if ($classRoomId) {
            \Illuminate\Support\Facades\Cache::forget("leaderboard_rankings_{$classRoomId}_monthly");
            \Illuminate\Support\Facades\Cache::forget("leaderboard_rankings_{$classRoomId}_all_time");
        }
        \Illuminate\Support\Facades\Cache::forget("leaderboard_rankings_global_monthly");
        \Illuminate\Support\Facades\Cache::forget("leaderboard_rankings_global_all_time");
    }

    /**
     * Recalculate and populate points/badges for ALL active students and ALL attendances.
     * Useful for initial sync of historical attendance data.
     */
    public function recalculateAllStudents(): void
    {
        $students = Student::where('is_active', true)->get();
        $streakService = app(AttendanceStreakService::class);

        foreach ($students as $student) {
            $streakService->recalculateStreak($student);
        }

        $this->recalculateRanks();
    }

    /**
     * Recalculate monthly rankings for students in a class or all active students.
     * Handles tied rankings appropriately.
     */
    public function recalculateRanks(?int $classRoomId = null): void
    {
        $query = Student::where('is_active', true);
        if ($classRoomId) {
            $query->where('class_room_id', $classRoomId);
        }

        $classIds = $query->distinct()->pluck('class_room_id')->filter();

        foreach ($classIds as $classId) {
            $studentsInClass = Student::where('class_room_id', $classId)
                ->where('is_active', true)
                ->orderByDesc('monthly_points')
                ->orderByDesc('total_points')
                ->orderByDesc('current_streak')
                ->orderByRaw('CASE WHEN avg_check_in_seconds IS NULL THEN 1 ELSE 0 END, avg_check_in_seconds ASC')
                ->orderBy('id')
                ->get();

            $currentRank = 1;
            $prevKey = null;

            foreach ($studentsInClass as $index => $st) {
                $key = "{$st->monthly_points}_{$st->total_points}_{$st->current_streak}_" . ($st->avg_check_in_seconds ?? 'null');

                if ($prevKey !== null && $key === $prevKey) {
                    $assignedRank = $currentRank;
                } else {
                    $assignedRank = $index + 1;
                    $currentRank = $assignedRank;
                }
                $prevKey = $key;

                $st->update(['monthly_rank' => $assignedRank]);

                if ($assignedRank === 1 && $st->monthly_points > 0) {
                    $this->awardBadge($st, 'top_class');
                }
            }

            $this->clearLeaderboardCache($classId);
        }
    }

    /**
     * Check condition thresholds and award any earned badges.
     */
    public function checkAndAwardBadges(Student $student): void
    {
        $student->refresh();

        $attendancesCount = Attendance::where('student_id', $student->id)
            ->whereIn('status', [AttendanceStatus::Hadir->value, AttendanceStatus::Terlambat->value])
            ->count();

        if ($attendancesCount >= 1) {
            $this->awardBadge($student, 'first_checkin');
        }
        if ($attendancesCount >= 10) {
            $this->awardBadge($student, 'hadir_10');
        }
        if ($attendancesCount >= 50) {
            $this->awardBadge($student, 'hadir_50');
        }
        if ($attendancesCount >= 100) {
            $this->awardBadge($student, 'hadir_100');
        }

        if ($student->current_streak >= 5) {
            $this->awardBadge($student, 'perfect_week');
        }

        if ($student->monthly_rank === 1 && $student->monthly_points > 0) {
            $this->awardBadge($student, 'top_class');
        }
    }

    /**
     * Award a specific badge key to a student if not already earned.
     */
    public function awardBadge(Student $student, string $badgeKey): bool
    {
        if (!isset(self::BADGES_DEFINITION[$badgeKey])) {
            return false;
        }

        $exists = StudentBadge::where('student_id', $student->id)
            ->where('badge_key', $badgeKey)
            ->exists();

        if ($exists) {
            return false;
        }

        StudentBadge::create([
            'student_id' => $student->id,
            'badge_key' => $badgeKey,
            'earned_at' => now(),
        ]);

        $badgeInfo = self::BADGES_DEFINITION[$badgeKey];

        Notification::create([
            'user_id' => $student->user_id,
            'type' => 'system',
            'title' => "Badge Baru Terbuka! {$badgeInfo['icon']}",
            'body' => "Selamat {$student->user->name}! Kamu telah mendapatkan badge '{$badgeInfo['name']}'! 🎉",
        ]);

        return true;
    }

    /**
     * Reset monthly points for all active students (called on 1st of month).
     */
    public function resetMonthlyPoints(): void
    {
        Student::query()->update([
            'monthly_points' => 0,
            'monthly_rank' => null,
            'avg_check_in_seconds' => null,
        ]);
        $this->clearLeaderboardCache();
    }
}
