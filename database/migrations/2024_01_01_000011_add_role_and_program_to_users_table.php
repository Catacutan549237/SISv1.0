<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'professor', 'student'])->default('student')->after('email');
            $table->foreignId('program_id')->nullable()->constrained()->onDelete('set null')->after('role');
            $table->string('student_id')->nullable()->unique()->after('program_id');
            $table->string('year_level')->nullable()->after('student_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropColumn(['role', 'program_id', 'student_id', 'year_level']);
        });
    }
};
