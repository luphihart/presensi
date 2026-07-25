<?php

namespace App\Jobs;

use App\Models\Student;
use App\Services\ImageCompressionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessProfilePhoto implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $studentId,
        public string $base64Photo
    ) {}

    public function handle(ImageCompressionService $compressionService): void
    {
        $student = Student::find($this->studentId);
        if (!$student) return;

        $path = $compressionService->compressBase64($this->base64Photo, 'profile-photos');
        $student->update(['profile_photo_path' => $path]);
    }
}
