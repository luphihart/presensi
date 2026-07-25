<?php

namespace App\Livewire\Student;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Services\GeofenceService;
use App\Services\ImageCompressionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class AttendanceCheckOut extends Component
{
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?float $distanceMeters = null;
    public bool $isWithinGeofence = false;
    public ?string $photoData = null;
    public ?string $errorMessage = null;
    public ?string $successMessage = null;
    public ?string $checkOutTime = null;

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
        $now = Carbon::now();
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

        if ($schedule && !empty($schedule->check_out_time)) {
            $checkOutStartTime = Carbon::parse($today->toDateString() . ' ' . $schedule->check_out_time);
            $this->checkOutTime = $checkOutStartTime->format('H:i');

            if ($now->lt($checkOutStartTime)) {
                $this->errorMessage = 'Presensi pulang belum dibuka. Jam pulang sekolah hari ini adalah pukul ' . $this->checkOutTime . ' WIB.';
                return false;
            }
        }

        return true;
    }

    public function submitCheckOut(GeofenceService $geofenceService, ImageCompressionService $compressionService): void
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

        $today = Carbon::today();
        $now = Carbon::now();

        $attendance = Attendance::where('student_id', $student->id)
            ->where('date', $today->toDateString())
            ->first();

        if (!$attendance || !$attendance->check_in_at) {
            $this->errorMessage = 'Anda belum melakukan presensi masuk hari ini.';
            return;
        }

        if ($attendance->check_out_at) {
            $this->errorMessage = 'Anda sudah melakukan presensi pulang hari ini pada jam ' . $attendance->check_out_at->format('H:i') . ' WIB.';
            return;
        }

        // Compress and save photo synchronously
        $photoPath = $compressionService->compressBase64($this->photoData, 'attendance-photos');

        $attendance->update([
            'check_out_at' => $now,
            'check_out_latitude' => $this->latitude,
            'check_out_longitude' => $this->longitude,
            'check_out_distance_meters' => $this->distanceMeters,
            'check_out_photo_path' => $photoPath,
        ]);

        $this->successMessage = 'Presensi pulang berhasil tercatat jam ' . $now->format('H:i') . ' WIB';
    }

    public function render()
    {
        return view('livewire.student.attendance-check-out');
    }
}
