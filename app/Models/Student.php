<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'class_room_id',
        'nis',
        'phone',
        'address',
        'birth_date',
        'profile_photo_path',
        'gender',
        'enrolled_at',
        'is_active',
        'current_streak',
        'longest_streak',
        'total_points',
        'monthly_points',
        'monthly_rank',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'enrolled_at' => 'date',
            'is_active' => 'boolean',
            'current_streak' => 'integer',
            'longest_streak' => 'integer',
            'total_points' => 'integer',
            'monthly_points' => 'integer',
            'monthly_rank' => 'integer',
        ];
    }

    public function getBadge(): ?array
    {
        return app(\App\Services\AttendanceStreakService::class)->getBadge($this->current_streak);
    }

    public function getNextMilestone(): ?array
    {
        return app(\App\Services\AttendanceStreakService::class)->getNextMilestone($this->current_streak);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function disciplinePoints(): HasMany
    {
        return $this->hasMany(DisciplinePoint::class);
    }

    public function badges(): HasMany
    {
        return $this->hasMany(StudentBadge::class);
    }
}
