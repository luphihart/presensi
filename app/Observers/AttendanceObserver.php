<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Models\AuditLog;

class AttendanceObserver
{
    public function updated(Attendance $attendance): void
    {
        if ($attendance->isDirty('is_manual_correction') && $attendance->is_manual_correction) {
            AuditLog::create([
                'actor_id' => $attendance->corrected_by ?? auth()->id() ?? 1,
                'action' => 'attendance.corrected',
                'subject_type' => Attendance::class,
                'subject_id' => $attendance->id,
                'old_values' => $attendance->getOriginal(),
                'new_values' => $attendance->getAttributes(),
                'ip_address' => request()->ip(),
            ]);
        }
    }
}
