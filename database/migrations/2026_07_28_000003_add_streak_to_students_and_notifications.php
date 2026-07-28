<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedInteger('current_streak')->default(0)->after('is_active');
            $table->unsignedInteger('longest_streak')->default(0)->after('current_streak');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('leave_status', 'absence_reminder', 'birthday', 'new_leave_request', 'announcement', 'streak_milestone', 'system') NOT NULL");
        }
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['current_streak', 'longest_streak']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('leave_status', 'absence_reminder', 'birthday', 'new_leave_request', 'announcement', 'system') NOT NULL");
        }
    }
};
