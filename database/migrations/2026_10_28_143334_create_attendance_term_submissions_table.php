<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_term_submissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();

            // ID from the teachers table, not the users table.
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();

            $table->foreignId('class_room_id')->constrained('class_rooms')->cascadeOnDelete();

            $table->unsignedBigInteger('section_id')->nullable();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();

            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable();

            $table->string('status', 20)->default('pending');
            // pending, approved, returned

            $table->unsignedInteger('total_records')->default(0);
            $table->unsignedInteger('present_count')->default(0);
            $table->unsignedInteger('absent_count')->default(0);
            $table->unsignedInteger('late_count')->default(0);
            $table->unsignedInteger('excused_count')->default(0);

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('teacher_remarks')->nullable();
            $table->text('admin_remarks')->nullable();

            $table->timestamps();

            $table->index([
                'school_id',
                'academic_session_id',
                'term_id',
                'status',
            ], 'attendance_term_submission_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_term_submissions');
    }
};