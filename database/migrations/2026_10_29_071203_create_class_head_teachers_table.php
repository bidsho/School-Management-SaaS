
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_head_teachers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->restrictOnDelete();

            $table->foreignId('class_room_id')
                ->constrained('class_rooms')
                ->restrictOnDelete();

            $table->foreignId('academic_session_id')
                ->constrained('academic_sessions')
                ->restrictOnDelete();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status', 20)->default('active');

            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();

            $table->timestamps();

            // One class head teacher assignment per class per session.
            $table->unique(
                ['school_id', 'class_room_id', 'academic_session_id'],
                'class_head_teacher_class_session_unique'
            );

            $table->index(['school_id', 'teacher_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_head_teachers');
    }
};
