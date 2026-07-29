<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discipline_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('attendance_id')->nullable()->constrained('attendances')->nullOnDelete();
            $table->foreignId('school_year_id')->nullable()->constrained('school_years')->nullOnDelete();
            $table->date('date')->index();
            $table->integer('points');
            $table->string('reason', 255);
            $table->timestamps();
        });

        Schema::create('student_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('badge_key', 64)->index();
            $table->timestamp('earned_at');
            $table->timestamps();

            $table->unique(['student_id', 'badge_key']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->unsignedInteger('total_points')->default(0)->after('longest_streak');
            $table->unsignedInteger('monthly_points')->default(0)->after('total_points');
            $table->unsignedInteger('monthly_rank')->nullable()->after('monthly_points');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['total_points', 'monthly_points', 'monthly_rank']);
        });

        Schema::dropIfExists('student_badges');
        Schema::dropIfExists('discipline_points');
    }
};
