<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageCompressionService
{
    /**
     * Process and compress base64 image from web camera capture.
     */
    public function compressBase64(string $base64Data, string $folder = 'attendance-photos'): string
    {
        // Strip data:image/jpeg;base64, header if present
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $base64Data = base64_decode($data);
        } else {
            $base64Data = base64_decode($base64Data);
        }

        $filename = $folder . '/' . Str::random(40) . '.webp';

        try {
            $img = Image::read($base64Data);
            // Resize to max 800px width/height maintaining aspect ratio
            $img->scaleDown(width: 800, height: 800);
            $encoded = $img->toWebp(75);

            Storage::disk('public')->put($filename, (string)$encoded);
            return $filename;
        } catch (\Throwable $e) {
            // Fallback if image intervention fails
            Storage::disk('public')->put($filename, $base64Data);
            return $filename;
        }
    }

    /**
     * Process uploaded file (e.g. profile photo).
     */
    public function compressFile($file, string $folder = 'profile-photos'): string
    {
        $filename = $folder . '/' . Str::random(40) . '.webp';

        try {
            $img = Image::read($file->getRealPath());
            $img->scaleDown(width: 600, height: 600);
            $encoded = $img->toWebp(80);

            Storage::disk('public')->put($filename, (string)$encoded);
            return $filename;
        } catch (\Throwable $e) {
            $path = $file->store($folder, 'public');
            return $path;
        }
    }
}
