<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_section_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending_payment', 'enrolled', 'dropped'])->default('pending_payment');
            $table->decimal('grade', 5, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'course_section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
