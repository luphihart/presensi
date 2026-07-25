<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Schedule;
use Carbon\Carbon;

class AttendanceStatusService
{
    /**
     * Determine status (Hadir vs Terlambat) based on check-in timestamp vs schedule.
     */
    public function determineStatus(Carbon $checkInAt, Schedule $schedule): AttendanceStatus
    {
        $checkInTimeStr = $schedule->check_in_time; // e.g. "07:00:00"
        $toleranceMinutes = $schedule->check_in_tolerance_minutes ?? 10;

        $scheduledCheckIn = Carbon::parse($checkInAt->toDateString() . ' ' . $checkInTimeStr);
        $latestAllowedTime = (clone $scheduledCheckIn)->addMinutes($toleranceMinutes);

        if ($checkInAt->greaterThan($latestAllowedTime)) {
            return AttendanceStatus::Terlambat;
        }

        return AttendanceStatus::Hadir;
    }
}
