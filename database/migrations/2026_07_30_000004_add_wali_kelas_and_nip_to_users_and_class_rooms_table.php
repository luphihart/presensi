<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'student', 'wali_kelas') NOT NULL DEFAULT 'student'");
        }

        if (!Schema::hasColumn('users', 'nip')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nip', 50)->nullable()->unique()->after('email');
            });
        }

        if (!Schema::hasColumn('class_rooms', 'wali_kelas_id')) {
            Schema::table('class_rooms', function (Blueprint $table) {
                $table->foreignId('wali_kelas_id')->nullable()->after('school_year_id')->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('class_rooms', 'wali_kelas_id')) {
            Schema::table('class_rooms', function (Blueprint $table) {
                $table->dropForeign(['wali_kelas_id']);
                $table->dropColumn('wali_kelas_id');
            });
        }

        if (Schema::hasColumn('users', 'nip')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nip');
            });
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'student') NOT NULL DEFAULT 'student'");
        }
    }
};

