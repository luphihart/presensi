<?php

namespace App\Livewire\Student;

use App\Jobs\ProcessAttendancePhoto;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Services\AttendanceStatusService;
use App\Services\GeofenceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class AttendanceCheckIn extends Component
{
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?float $distanceMeters = null;
    public bool $isWithinGeofence = false;
    public ?string $photoData = null;
    public ?string $errorMessage = null;
    public ?string $successMessage = null;

    public function setLocation(float $lat, float $lng, GeofenceService $geofenceService): void
    {
        $this->latitude = $lat;
        $this->longitude = $lng;

        $check = $geofenceService->checkLocation($lat, $lng);
        $this->isWithinGeofence = $check['is_valid'];
        $this->distanceMeters = $check['distance_meters'];
    }

    public function setPhoto(string $base64): void
    {
        $this->photoData = $base64;
    }

    public function clearPhoto(): void
    {
        $this->photoData = null;
    }

    public function mount(): void
    {
        $this->checkSchoolDay();
    }

    private function checkSchoolDay(): bool
    {
        $today = Carbon::today();
        $schoolYear = SchoolYear::getActive();

        $holiday = \App\Models\Holiday::whereDate('date', $today->toDateString())
            ->first();

        if ($holiday) {
            $this->errorMessage = 'Hari ini (' . $holiday->name . ') adalah hari libur. Presensi tidak dapat dilakukan.';
            return false;
        }

        $schedule = Schedule::where('school_year_id', $schoolYear?->id)
            ->where('day_of_week', $today->dayOfWeek)
            ->first();

        if ($schedule && !$schedule->is_school_day) {
            $this->errorMessage = 'Hari ini bukan hari sekolah / libur. Presensi tidak dapat dilakukan.';
            return false;
        }

        return true;
    }

    public function submitCheckIn(AttendanceStatusService $statusService, GeofenceService $geofenceService): void
    {
        if (!$this->checkSchoolDay()) {
            return;
        }

        $user = Auth::user();
        $student = $user->student;
        if (!$student) {
            $this->errorMessage = 'Data murid tidak ditemukan.';
            return;
        }

        if (empty($this->photoData)) {
            $this->errorMessage = 'Silakan ambil foto selfie Anda terlebih dahulu.';
            return;
        }

        // Validate Geofence again on server side
        if ($this->latitude && $this->longitude) {
            $check = $geofenceService->checkLocation($this->latitude, $this->longitude);
            if (!$check['is_valid']) {
                $this->errorMessage = 'Anda berada di luar radius sekolah (' . round($check['distance_meters']) . ' meter). Presensi ditolak.';
                return;
            }
        }

        $today = Carbon::today();
        $now = Carbon::now();
        $schoolYear = SchoolYear::getActive();

        // Check duplicate
        $existing = Attendance::where('student_id', $student->id)
            ->where('date', $today->toDateString())
            ->first();

        if ($existing && $existing->check_in_at) {
            $this->errorMessage = 'Anda sudah melakukan presensi masuk hari ini pada jam ' . $existing->check_in_at->format('H:i') . ' WIB.';
            return;
        }

        $schedule = Schedule::where('school_year_id', $schoolYear?->id)
            ->where('day_of_week', $today->dayOfWeek)
            ->first();

        $status = $schedule ? $statusService->determineStatus($now, $schedule) : \App\Enums\AttendanceStatus::Hadir;

        $attendance = Attendance::updateOrCreate(
            ['student_id' => $student->id, 'date' => $today->toDateString()],
            [
                'school_year_id' => $schoolYear?->id ?? 1,
                'check_in_at' => $now,
                'check_in_latitude' => $this->latitude,
                'check_in_longitude' => $this->longitude,
                'check_in_distance_meters' => $this->distanceMeters,
                'status' => $status,
            ]
        );

        // Dispatch async photo processing job
        dispatch(new ProcessAttendancePhoto($attendance->id, $this->photoData, 'check_in'));

        $this->successMessage = 'Presensi masuk berhasil tercatat jam ' . $now->format('H:i') . ' WIB (' . $status->label() . ')';
    }

    public function render()
    {
        return view('livewire.student.attendance-check-in');
    }
}
