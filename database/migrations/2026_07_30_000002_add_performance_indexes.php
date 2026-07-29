<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->index('student_id');
            $table->index('school_year_id');
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->index('student_id');
            $table->index(['student_id', 'status']);
            $table->index(['student_id', 'date']);
        });

        Schema::table('discipline_points', function (Blueprint $table) {
            $table->index('student_id');
            $table->index(['student_id', 'date']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->index('is_active');
            $table->index(['class_room_id', 'is_active']);
            $table->index('monthly_points');
            $table->index('total_points');
            $table->index('current_streak');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['class_room_id', 'is_active']);
            $table->dropIndex(['monthly_points']);
            $table->dropIndex(['total_points']);
            $table->dropIndex(['current_streak']);
        });

        Schema::table('discipline_points', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['student_id', 'date']);
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['student_id', 'status']);
            $table->dropIndex(['student_id', 'date']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['school_year_id']);
        });
    }
};
