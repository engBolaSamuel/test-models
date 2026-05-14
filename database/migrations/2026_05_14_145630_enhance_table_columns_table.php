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
        Schema::table('table_columns', function (Blueprint $table) {
            $table->boolean('is_nullable')->default(false)->after('is_primary');
            $table->string('default_value')->nullable()->after('is_nullable');
            $table->boolean('is_unsigned')->default(false)->after('default_value');
            $table->integer('length')->nullable()->after('is_unsigned');
            $table->smallInteger('position')->default(0)->after('length');
            $table->string('index_type', 20)->nullable()->after('position');
            $table->string('fk_table')->nullable()->after('index_type');
            $table->string('fk_column')->nullable()->after('fk_table');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_columns', function (Blueprint $table) {
            $table->dropColumn([
                'is_nullable',
                'default_value',
                'is_unsigned',
                'length',
                'position',
                'index_type',
                'fk_table',
                'fk_column',
            ]);
        });
    }
};
