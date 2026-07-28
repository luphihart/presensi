<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('leave_status', 'absence_reminder', 'birthday', 'new_leave_request', 'announcement', 'system') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('leave_status', 'absence_reminder', 'birthday', 'new_leave_request', 'system') NOT NULL");
        }
    }
};
