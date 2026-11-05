<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->date('transaction_date');
            $table->string('description');
            $table->string('reference')->nullable();

            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('NGN');

            // A transaction is unmatched until a staff member confirms a match.
            $table->string('status', 20)->default('unmatched');

            // A payment can be reconciled only once through this table.
            $table->foreignId('payment_id')
                ->nullable()
                ->unique()
                ->constrained('payments')
                ->nullOnDelete();

            $table->foreignId('matched_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('matched_at')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'transaction_date']);
            $table->index(['school_id', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};