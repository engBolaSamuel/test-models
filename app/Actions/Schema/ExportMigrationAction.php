<?php

namespace App\Actions\Schema;

use App\Models\Project;
use Illuminate\Support\Str;

class ExportMigrationAction
{
    public function execute(Project $project): string
    {
        $project->load(['tables.columns', 'pivotRelationships']);

        $output = "<?php\n\n";
        $output .= "use Illuminate\Database\Migrations\Migration;\n";
        $output .= "use Illuminate\Database\Schema\Blueprint;\n";
        $output .= "use Illuminate\Support\Facades\Schema;\n\n";
        $output .= "return new class extends Migration\n";
        $output .= "{\n";
        $output .= "    public function up(): void\n";
        $output .= "    {\n";

        // Create standard tables
        foreach ($project->tables as $table) {
            $output .= "        Schema::create('{$table->name}', function (Blueprint \$table) {\n";
            $output .= "            \$table->id();\n";

            foreach ($table->columns->sortBy('position') as $column) {
                if ($column->name === 'id') {
                    continue;
                }
                if ($column->name === 'created_at' || $column->name === 'updated_at') {
                    continue;
                }

                $type = $column->type->value;

                $line = "            \$table->{$type}('{$column->name}'";
                if ($column->length && $type === 'string') {
                    $line .= ", {$column->length}";
                }
                $line .= ')';

                if ($column->is_unsigned && ! str_contains(strtolower($type), 'unsigned')) {
                    $line .= '->unsigned()';
                }
                if ($column->is_nullable) {
                    $line .= '->nullable()';
                }
                if ($column->default_value !== null) {
                    if (is_numeric($column->default_value)) {
                        $line .= "->default({$column->default_value})";
                    } else {
                        $line .= "->default('{$column->default_value}')";
                    }
                }

                $line .= ";\n";
                $output .= $line;

                if ($column->is_primary && $column->name !== 'id') {
                    $output .= "            \$table->primary('{$column->name}');\n";
                }

                if ($column->index_type) {
                    $idx = $column->index_type->value;
                    if ($idx === 'unique' || $idx === 'index') {
                        $output .= "            \$table->{$idx}('{$column->name}');\n";
                    }
                }

                if ($column->fk_table && $column->fk_column) {
                    $output .= "            \$table->foreign('{$column->name}')->references('{$column->fk_column}')->on('{$column->fk_table}');\n";
                }
            }

            $hasTimestamps = $table->columns->contains('name', 'created_at') && $table->columns->contains('name', 'updated_at');
            if ($hasTimestamps) {
                $output .= "            \$table->timestamps();\n";
            }

            $output .= "        });\n\n";
        }

        // Create pivot tables
        foreach ($project->pivotRelationships as $pivot) {
            $output .= "        Schema::create('{$pivot->pivot_table_name}', function (Blueprint \$table) {\n";
            $output .= "            \$table->id();\n";
            $tableOneName = $pivot->tableOne->name;
            $tableTwoName = $pivot->tableTwo->name;
            $output .= "            \$table->foreignId('".Str::singular($tableOneName)."_id')->constrained('{$tableOneName}')->cascadeOnDelete();\n";
            $output .= "            \$table->foreignId('".Str::singular($tableTwoName)."_id')->constrained('{$tableTwoName}')->cascadeOnDelete();\n";

            if ($pivot->with_timestamps) {
                $output .= "            \$table->timestamps();\n";
            }
            $output .= "        });\n\n";
        }

        $output .= "    }\n\n";

        $output .= "    public function down(): void\n";
        $output .= "    {\n";

        foreach ($project->pivotRelationships as $pivot) {
            $output .= "        Schema::dropIfExists('{$pivot->pivot_table_name}');\n";
        }

        foreach ($project->tables as $table) {
            $output .= "        Schema::dropIfExists('{$table->name}');\n";
        }

        $output .= "    }\n";
        $output .= "};\n";

        return $output;
    }
}
