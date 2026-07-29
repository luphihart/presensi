<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\LeaveStatus;
use App\Enums\NotificationType;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Models\Student;
use Carbon\Carbon;

class AttendanceStreakService
{
    /**
     * Milestone thresholds & badges.
     */
    public const MILESTONES = [
        5   => ['icon' => '🔥', 'name' => 'On Fire', 'color' => 'amber'],
        10  => ['icon' => '⚡', 'name' => 'Konsisten', 'color' => 'indigo'],
        20  => ['icon' => '🌟', 'name' => 'Rajin Banget', 'color' => 'purple'],
        30  => ['icon' => '🏆', 'name' => 'Iron Attendance', 'color' => 'emerald'],
        50  => ['icon' => '💎', 'name' => 'Legend', 'color' => 'cyan'],
        100 => ['icon' => '👑', 'name' => 'Hadir Champion', 'color' => 'rose'],
    ];

    /**
     * Recalculates current_streak & longest_streak for a given student.
     */
    public function recalculateStreak(Student $student, ?array $holidays = null, ?array $schedules = null): array
    {
        $schoolYear = SchoolYear::getActive();

        $today = Carbon::today();

        // Fetch holidays & schedules if not pre-loaded
        $schoolYearStart = null;

        if ($schoolYear) {
            if ($holidays === null) {
                $holidays = Holiday::where('school_year_id', $schoolYear->id)
                    ->pluck('date')
                    ->map(fn($d) => Carbon::parse($d)->toDateString())
                    ->toArray();
            }

            if ($schedules === null) {
                $schedules = Schedule::where('school_year_id', $schoolYear->id)
                    ->pluck('is_school_day', 'day_of_week')
                    ->toArray();
            }

            // Cap startDate to school year start so old data from previous years
            // does not create massive gaps of implicit 'Alpa'
            $schoolYearStart = Carbon::parse($schoolYear->start_date);
        } else {
            $holidays = $holidays ?? [];
            $schedules = $schedules ?? [];
        }

        // Determine start date: earliest attendance/leave within this school year
        $attQuery = Attendance::where('student_id', $student->id);
        $leaveQuery = LeaveRequest::where('student_id', $student->id);

        if ($schoolYearStart) {
            $attQuery->whereDate('date', '>=', $schoolYearStart->toDateString());
            $leaveQuery->whereDate('date', '>=', $schoolYearStart->toDateString());
        }

        $earliestAtt   = $attQuery->min('date');
        $earliestLeave = $leaveQuery->min('date');

        $dates = array_filter([$earliestAtt, $earliestLeave]);
        if (!empty($dates)) {
            $startDate = Carbon::parse(min($dates));
        } else {
            // No records yet — nothing to calculate, preserve current value
            return [
                'current' => (int)($student->current_streak ?? 0),
                'longest' => (int)($student->longest_streak ?? 0),
            ];
        }

        // Cap to school year start if applicable
        if ($schoolYearStart && $startDate->lt($schoolYearStart)) {
            $startDate = $schoolYearStart->copy();
        }

        if ($startDate->isAfter($today)) {
            // Start date is in the future — preserve existing streak, do not reset
            return [
                'current' => (int)($student->current_streak ?? 0),
                'longest' => (int)($student->longest_streak ?? 0),
            ];
        }

        // Fetch attendances within range
        $attendances = Attendance::where('student_id', $student->id)
            ->whereDate('date', '>=', $startDate->toDateString())
            ->whereDate('date', '<=', $today->toDateString())
            ->get()
            ->keyBy(function ($item) {
                return $item->date instanceof \Carbon\CarbonInterface
                    ? $item->date->toDateString()
                    : Carbon::parse($item->date)->toDateString();
            });

        // Fetch all approved leave requests
        $approvedLeaves = LeaveRequest::where('student_id', $student->id)
            ->where('status', LeaveStatus::Approved)
            ->whereDate('date', '>=', $startDate->toDateString())
            ->whereDate('date', '<=', $today->toDateString())
            ->get()
            ->map(function ($item) {
                return $item->date instanceof \Carbon\CarbonInterface
                    ? $item->date->toDateString()
                    : Carbon::parse($item->date)->toDateString();
            })
            ->toArray();

        // Build list of valid school days chronologically
        $schoolDays = [];
        $cursor = $startDate->copy();
        while ($cursor->lte($today)) {
            $dateStr   = $cursor->toDateString();
            $dayOfWeek = $cursor->dayOfWeek; // 0 = Sunday

            $isSchoolDay = isset($schedules[$dayOfWeek])
                ? (bool)$schedules[$dayOfWeek]
                : ($dayOfWeek >= 1 && $dayOfWeek <= 5);
            $isHoliday = in_array($dateStr, $holidays, true);

            if ($isSchoolDay && !$isHoliday) {
                $schoolDays[] = $dateStr;
            }
            $cursor->addDay();
        }

        if (empty($schoolDays)) {
            // No school days in the calculated range — preserve existing value, do NOT reset
            return [
                'current' => (int)($student->current_streak ?? 0),
                'longest' => (int)($student->longest_streak ?? 0),
            ];
        }

        // Now calculate streaks chronologically
        $currentStreak = 0;
        $longestStreak = 0;

        foreach ($schoolDays as $dateStr) {
            $att = $attendances->get($dateStr);
            $hasLeave = in_array($dateStr, $approvedLeaves, true);

            if ($att) {
                app(\App\Services\DisciplinePointService::class)->syncAttendancePoints($student, $att, true);
                $statusVal = is_string($att->status) ? $att->status : $att->status->value;
                if (in_array($statusVal, [AttendanceStatus::Hadir->value, AttendanceStatus::Terlambat->value], true)) {
                    $currentStreak++;
                } elseif (in_array($statusVal, [AttendanceStatus::Izin->value, AttendanceStatus::Sakit->value], true) || $hasLeave) {
                    // Approved leave preserves streak
                    $currentStreak++;
                } else {
                    // Alpa
                    $currentStreak = 0;
                }
            } elseif ($hasLeave) {
                // Approved leave preserves streak
                $currentStreak++;
            } else {
                // Missed school day without attendance record/leave = Alpa
                $currentStreak = 0;
            }

            if ($currentStreak > $longestStreak) {
                $longestStreak = $currentStreak;
            }
        }

        $oldStreak = (int)($student->current_streak ?? 0);
        $student->update([
            'current_streak' => $currentStreak,
            'longest_streak' => max($longestStreak, (int)($student->longest_streak ?? 0)),
        ]);

        app(\App\Services\DisciplinePointService::class)->recalculateStudentTotals($student);
        app(\App\Services\DisciplinePointService::class)->checkAndAwardBadges($student);

        $this->checkAndNotifyMilestone($student, $oldStreak, $currentStreak);

        return ['current' => $currentStreak, 'longest' => $student->longest_streak];
    }

    /**
     * Send milestone notification if a new milestone threshold is crossed.
     */
    public function checkAndNotifyMilestone(Student $student, int $oldStreak, int $newStreak): void
    {
        foreach (self::MILESTONES as $threshold => $badge) {
            if ($oldStreak < $threshold && $newStreak >= $threshold) {
                $notificationType = defined('\App\Enums\NotificationType::StreakMilestone') 
                    ? NotificationType::StreakMilestone 
                    : NotificationType::System;

                $alreadyNotified = Notification::where('user_id', $student->user_id)
                    ->where('body', 'like', "%{$badge['name']}%")
                    ->exists();

                if (!$alreadyNotified) {
                    Notification::create([
                        'user_id' => $student->user_id,
                        'type' => $notificationType,
                        'title' => "Milestone Streak Terbuka! {$badge['icon']}",
                        'body' => "Selamat {$student->user->name}! Kamu berhasil mencapai streak {$newStreak} hari ({$badge['name']}). Pertahankan kebiasaan hebat ini! 🚀",
                    ]);
                }
            }
        }
    }

    /**
     * Get badge detail for a streak count.
     */
    public function getBadge(int $streak): ?array
    {
        $currentBadge = null;
        foreach (self::MILESTONES as $threshold => $badge) {
            if ($streak >= $threshold) {
                $currentBadge = array_merge($badge, ['threshold' => $threshold]);
            }
        }
        return $currentBadge;
    }

    /**
     * Get next milestone target for progress bar.
     */
    public function getNextMilestone(int $streak): ?array
    {
        foreach (self::MILESTONES as $threshold => $badge) {
            if ($streak < $threshold) {
                return array_merge($badge, ['threshold' => $threshold]);
            }
        }
        return null;
    }
}
