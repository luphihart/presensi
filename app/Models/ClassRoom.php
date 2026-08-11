<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_year_id',
        'wali_kelas_id',
        'name',
        'major',
    ];

    public function getFullNameAttribute(): string
    {
        if (!empty($this->major) && !str_contains(strtolower($this->name), strtolower($this->major))) {
            return "{$this->name} {$this->major}";
        }
        return $this->name ?? '';
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
