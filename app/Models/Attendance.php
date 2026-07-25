<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'school_year_id',
        'date',
        'check_in_at',
        'check_in_photo_path',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_distance_meters',
        'check_out_at',
        'check_out_photo_path',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_distance_meters',
        'status',
        'is_manual_correction',
        'corrected_by',
        'correction_reason',
        'leave_request_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'check_in_latitude' => 'float',
            'check_in_longitude' => 'float',
            'check_in_distance_meters' => 'float',
            'check_out_latitude' => 'float',
            'check_out_longitude' => 'float',
            'check_out_distance_meters' => 'float',
            'status' => AttendanceStatus::class,
            'is_manual_correction' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function corrector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    public function leaveRequest(): BelongsTo
    {
        return $this->belongsTo(LeaveRequest::class);
    }
}
