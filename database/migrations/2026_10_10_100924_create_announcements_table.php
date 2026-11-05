<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');
            $table->longText('body');
            $table->string('audience')->default('all');
            $table->timestamp('published_at')->nullable();
            $table->string('status')->default('draft');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'school_id',
                'status',
                'published_at'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};