<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('students', 'middle_name')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('middle_name', 100)->nullable();
            });
        }
    }

    public function down(): void
    {
        // Intentionally leave the column in place to avoid
        // accidentally deleting existing student data.
    }
};
