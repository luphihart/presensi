<?php

namespace Tests\Unit;

use App\Enums\AttendanceStatus;
use App\Models\Schedule;
use App\Services\AttendanceStatusService;
use Carbon\Carbon;
use Tests\TestCase;

class AttendanceStatusServiceTest extends TestCase
{
    public function test_status_is_hadir_when_on_time(): void
    {
        $service = new AttendanceStatusService();
        $schedule = new Schedule([
            'check_in_time' => '07:00:00',
            'check_in_tolerance_minutes' => 10,
        ]);

        $checkInTime = Carbon::parse('2026-07-25 07:05:00');
        $status = $service->determineStatus($checkInTime, $schedule);

        $this->assertEquals(AttendanceStatus::Hadir, $status);
    }

    public function test_status_is_terlambat_when_past_tolerance(): void
    {
        $service = new AttendanceStatusService();
        $schedule = new Schedule([
            'check_in_time' => '07:00:00',
            'check_in_tolerance_minutes' => 10,
        ]);

        $checkInTime = Carbon::parse('2026-07-25 07:15:00');
        $status = $service->determineStatus($checkInTime, $schedule);

        $this->assertEquals(AttendanceStatus::Terlambat, $status);
    }
}
