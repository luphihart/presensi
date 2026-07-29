<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinePoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'attendance_id',
        'school_year_id',
        'date',
        'points',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'points' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
