<?php

namespace App\Actions\Mermaid;

use App\Enums\ColumnType;
use App\Enums\IndexType;
use App\Models\Project;
use Illuminate\Support\Str;

class GenerateMermaidAction
{
    /**
     * Generate a Mermaid ER diagram DSL string for the given project.
     */
    public function execute(Project $project): string
    {
        $project->load(['tables.columns', 'pivotRelationships.tableOne', 'pivotRelationships.tableTwo']);

        if ($project->tables->isEmpty()) {
            return '';
        }

        $lines = ['erDiagram'];
        $fkLines = [];

        foreach ($project->tables as $table) {
            $lines[] = "    {$table->name} {";

            foreach ($table->columns as $column) {
                $type = $this->mapColumnType($column->type);
                $modifiers = [];

                if ($column->index_type === IndexType::Primary) {
                    $modifiers[] = 'PK';
                }

                if ($column->fk_table && $column->fk_column) {
                    $modifiers[] = 'FK';
                    $fkLines[] = "    {$column->fk_table} ||--o{ {$table->name} : \"{$column->name}\"";
                }

                $modifierStr = empty($modifiers) ? '' : ' '.implode(',', $modifiers);
                $lines[] = "        {$type} {$column->name}{$modifierStr}";
            }

            $lines[] = '    }';
        }

        foreach ($fkLines as $fkLine) {
            $lines[] = $fkLine;
        }

        foreach ($project->pivotRelationships as $pivot) {
            $t1Name = $pivot->tableOne->name;
            $t2Name = $pivot->tableTwo->name;
            $pivotName = $pivot->pivot_table_name;

            $lines[] = "    {$pivotName} {";

            $t1Fk = Str::singular($t1Name).'_id';
            $t2Fk = Str::singular($t2Name).'_id';

            $lines[] = "        bigint {$t1Fk} FK";
            $lines[] = "        bigint {$t2Fk} FK";

            if ($pivot->with_timestamps) {
                $lines[] = '        timestamp created_at';
                $lines[] = '        timestamp updated_at';
            }

            $lines[] = '    }';

            $lines[] = "    {$t1Name} ||--o{ {$pivotName} : \"has\"";
            $lines[] = "    {$t2Name} ||--o{ {$pivotName} : \"has\"";
        }

        return implode("\n", $lines);
    }

    private function mapColumnType(ColumnType $type): string
    {
        return match ($type) {
            ColumnType::BigInteger => 'bigint',
            ColumnType::Boolean => 'bool',
            ColumnType::Date => 'date',
            ColumnType::DateTime => 'datetime',
            ColumnType::Decimal => 'decimal',
            ColumnType::Float => 'float',
            ColumnType::Integer => 'int',
            ColumnType::Json => 'json',
            ColumnType::SmallInteger => 'smallint',
            ColumnType::String => 'varchar',
            ColumnType::Text => 'text',
            ColumnType::Timestamp => 'timestamp',
            ColumnType::UnsignedBigInteger => 'ubigint',
        };
    }
}
