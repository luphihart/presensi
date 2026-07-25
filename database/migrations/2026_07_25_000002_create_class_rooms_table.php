<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->string('name', 50);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['school_year_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
};
