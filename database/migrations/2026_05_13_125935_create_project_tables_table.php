<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema; // تمت الإضافة هنا

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_tables', function (Blueprint $table) {
            $table->id();

            // ربط هذا الجدول بالمشروع الخاص به (Foreign Key)
            $table->foreignIdFor(Project::class)->constrained()->cascadeOnDelete();

            $table->string('name'); // اسم الجدول (مثلاً: users أو posts)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tables');
    }
};
