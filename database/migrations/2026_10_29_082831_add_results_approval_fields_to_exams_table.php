
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->string('results_status', 20)
                ->default('not_submitted');

            $table->foreignId('results_submitted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('results_submitted_at')->nullable();

            $table->foreignId('results_reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('results_reviewed_at')->nullable();

            $table->text('results_review_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['results_submitted_by']);
            $table->dropForeign(['results_reviewed_by']);

            $table->dropColumn([
                'results_status',
                'results_submitted_by',
                'results_submitted_at',
                'results_reviewed_by',
                'results_reviewed_at',
                'results_review_note',
            ]);
        });
    }
};
