<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->restrictOnDelete();

            $table->foreignId('payment_id')
                ->unique()
                ->constrained('payments')
                ->restrictOnDelete();

            $table->string('receipt_number');
            $table->timestamp('issued_at')->nullable();
            $table->string('file_path')->nullable();

            $table->timestamps();

            $table->unique([
                'school_id',
                'receipt_number'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};