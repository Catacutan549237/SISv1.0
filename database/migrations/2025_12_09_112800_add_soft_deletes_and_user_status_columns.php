<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add soft deletes to specified tables
        Schema::table('departments', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('programs', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('semesters', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('course_sections', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('enrollments', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('announcements', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add deactivation columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('remember_token');
            $table->text('deactivation_reason')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('programs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('semesters', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'deactivation_reason']);
        });
    }
};
