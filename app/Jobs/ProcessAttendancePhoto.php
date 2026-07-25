<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Services\ImageCompressionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessAttendancePhoto implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $attendanceId,
        public string $base64Photo,
        public string $type = 'check_in' // 'check_in' or 'check_out'
    ) {}

    public function handle(ImageCompressionService $compressionService): void
    {
        $attendance = Attendance::find($this->attendanceId);
        if (!$attendance) return;

        $path = $compressionService->compressBase64($this->base64Photo, 'attendance-photos');

        if ($this->type === 'check_in') {
            $attendance->update(['check_in_photo_path' => $path]);
        } else {
            $attendance->update(['check_out_photo_path' => $path]);
        }
    }
}
