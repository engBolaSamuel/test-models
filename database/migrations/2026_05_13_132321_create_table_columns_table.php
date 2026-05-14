<?php

use App\Models\ProjectTable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema; // تمت الإضافة هنا

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_columns', function (Blueprint $table) {
            $table->id();

            // ربط هذا العمود بالجدول الخاص به (Foreign Key)
            $table->foreignIdFor(ProjectTable::class)->constrained()->cascadeOnDelete();

            $table->string('name'); // اسم العمود (مثلاً: title, price)
            $table->string('type'); // نوع البيانات (مثلاً: string, integer, boolean)

            // تحديد ما إذا كان هذا العمود هو المفتاح الأساسي (Primary Key)
            $table->boolean('is_primary')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_columns');
    }
};
