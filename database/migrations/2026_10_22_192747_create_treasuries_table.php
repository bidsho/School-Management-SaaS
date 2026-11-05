
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treasuries', function (Blueprint $table) {
            $table->id();

            // School this Treasury staff member belongs to.
            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            // Existing Laravel login account.
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('employee_number', 50);
            $table->string('phone', 30)->nullable();
            $table->string('job_title')->default('Treasury Officer');

            $table->enum('status', ['active', 'inactive'])
                ->default('active');

            $table->date('date_joined')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Employee numbers must be unique within each school.
            $table->unique(
                ['school_id', 'employee_number'],
                'treasuries_school_employee_unique'
            );

            $table->index(['school_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treasuries');
    }
};
