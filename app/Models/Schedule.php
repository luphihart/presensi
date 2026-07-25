<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_year_id',
        'day_of_week',
        'check_in_time',
        'check_in_tolerance_minutes',
        'check_out_time',
        'is_school_day',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'check_in_tolerance_minutes' => 'integer',
            'is_school_day' => 'boolean',
        ];
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
