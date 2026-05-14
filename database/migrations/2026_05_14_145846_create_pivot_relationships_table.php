<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pivot_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_one_id')->constrained('project_tables')->cascadeOnDelete();
            $table->foreignId('table_two_id')->constrained('project_tables')->cascadeOnDelete();
            $table->string('pivot_table_name');
            $table->boolean('with_timestamps')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_relationships');
    }
};
