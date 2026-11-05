<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('staff_number');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('qualification')->nullable();

            $table->string('employment_status')->default('active');
            $table->date('hired_at')->nullable();

            $table->timestamps();

            $table->unique([
                'school_id',
                'staff_number'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};