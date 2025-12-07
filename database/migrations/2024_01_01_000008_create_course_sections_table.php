<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('professor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->string('section_code'); // e.g., "0001", "0002"
            $table->integer('max_students')->default(40);
            $table->string('schedule')->nullable(); // e.g., "MWF 9:00-10:00"
            $table->string('room')->nullable();
            $table->timestamps();
            
            $table->unique(['course_id', 'semester_id', 'section_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_sections');
    }
};
