
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('principals', function (Blueprint $table) {
            $table->id();

            // School and login account
            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            // Personal information
            $table->string('staff_number');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('phone', 30)->nullable();
            $table->string('gender', 30)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();

            // Employment information
            $table->string('qualification')->nullable();
            $table->string('employment_status')->default('active');
            $table->date('hired_at')->nullable();

            // Bank details for salary payment
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->text('bank_account_number')->nullable();
            $table->string('bank_code')->nullable();
            $table->text('payment_notes')->nullable();

            $table->timestamps();

            // Staff number is unique within each school
            $table->unique(
                ['school_id', 'staff_number'],
                'principals_school_staff_unique'
            );

            $table->index(
                ['school_id', 'employment_status'],
                'principals_school_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('principals');
    }
};
