
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_promotions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->restrictOnDelete();

            // Preserve the exact enrollment being reviewed.
            $table->foreignId('from_enrollment_id')
                ->constrained('enrollments')
                ->restrictOnDelete();

            $table->foreignId('from_academic_session_id')
                ->constrained('academic_sessions')
                ->restrictOnDelete();

            $table->foreignId('from_class_room_id')
                ->constrained('class_rooms')
                ->restrictOnDelete();

            // Destination session is required; destination class can
            // remain empty while a recommendation is under review.
            $table->foreignId('to_academic_session_id')
                ->constrained('academic_sessions')
                ->restrictOnDelete();

            $table->foreignId('to_class_room_id')
                ->nullable()
                ->constrained('class_rooms')
                ->restrictOnDelete();

            // promote, repeat, or review
            $table->string('decision', 20)->nullable();

            // draft, submitted, approved, rejected, executed
            $table->string('status', 20)->default('draft');

            $table->text('recommendation_reason')->nullable();
            $table->text('approval_note')->nullable();

            $table->foreignId('recommended_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('recommended_at')->nullable();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamp('executed_at')->nullable();

            $table->timestamps();

            // Prevent duplicate decisions for the same student,
            // source enrollment and destination academic session.
            $table->unique(
                [
                    'school_id',
                    'student_id',
                    'from_enrollment_id',
                    'to_academic_session_id',
                ],
                'student_promotion_source_session_unique'
            );

            $table->index(
                ['school_id', 'status', 'from_class_room_id'],
                'student_promotion_review_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_promotions');
    }
};
