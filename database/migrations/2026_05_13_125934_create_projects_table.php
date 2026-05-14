<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema; // أضفنا هذا السطر للوصول إلى موديل المستخدم

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            // ربط المشروع بالمستخدم (Foreign Key)
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();

            $table->string('name'); // اسم المشروع
            $table->text('description')->nullable(); // وصف اختياري

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
