<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_promotions', function (Blueprint $table) {
            $table->foreignId('to_academic_session_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        // Do not force this column back to NOT NULL automatically.
        // Existing recommendations may not have a destination session yet.
    }
};