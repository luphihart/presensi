<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->unsignedSmallInteger('day_of_week'); // 0=Sunday, 6=Saturday
            $table->time('check_in_time');
            $table->unsignedSmallInteger('check_in_tolerance_minutes')->default(10);
            $table->time('check_out_time');
            $table->boolean('is_school_day')->default(true);
            $table->timestamps();

            $table->unique(['school_year_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
