<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedInteger('avg_check_in_seconds')->nullable()->after('monthly_rank');
            $table->index('avg_check_in_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['avg_check_in_seconds']);
            $table->dropColumn('avg_check_in_seconds');
        });
    }
};
