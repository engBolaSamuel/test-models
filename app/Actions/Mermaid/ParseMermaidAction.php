<?php

namespace App\Actions\Mermaid;

use App\DTOs\ColumnDefinition;
use App\DTOs\PivotDefinition;
use App\DTOs\TableDefinition;
use App\Enums\ColumnType;
use App\Enums\IndexType;
use Illuminate\Support\Str;

class ParseMermaidAction
{
    /**
     * @return array{tables: array<int, TableDefinition>, pivots: array<int, PivotDefinition>}
     */
    public function execute(string $dsl): array
    {
        $relationships = $this->extractRelationships($dsl);
        $rawTables = $this->extractTables($dsl);

        $tables = [];
        $pivots = [];

        foreach ($rawTables as $tableName => $columns) {
            $incomingRels = array_filter($relationships, fn ($rel) => $rel['to'] === $tableName);

            if (count($incomingRels) === 2) {
                // Potential Pivot Table
                $inRels = array_values($incomingRels);
                $tableOne = $inRels[0]['from'];
                $tableTwo = $inRels[1]['from'];

                $hasCreatedAt = collect($columns)->contains(fn ($c) => $c->name === 'created_at');
                $hasUpdatedAt = collect($columns)->contains(fn ($c) => $c->name === 'updated_at');

                $pivots[] = new PivotDefinition(
                    tableOne: $tableOne,
                    tableTwo: $tableTwo,
                    pivotTableName: $tableName,
                    withTimestamps: $hasCreatedAt && $hasUpdatedAt
                );
            } else {
                // Check if it's implicitly a pivot table based on Laravel naming convention
                $parts = explode('_', $tableName);
                if (count($parts) === 2) {
                    $pluralOne = Str::plural($parts[0]);
                    $pluralTwo = Str::plural($parts[1]);

                    if (array_key_exists($pluralOne, $rawTables) && array_key_exists($pluralTwo, $rawTables)) {
                        $hasCreatedAt = collect($columns)->contains(fn ($c) => $c->name === 'created_at');
                        $hasUpdatedAt = collect($columns)->contains(fn ($c) => $c->name === 'updated_at');

                        $pivots[] = new PivotDefinition(
                            tableOne: $pluralOne,
                            tableTwo: $pluralTwo,
                            pivotTableName: $tableName,
                            withTimestamps: $hasCreatedAt && $hasUpdatedAt
                        );

                        continue;
                    }
                }

                // Standard Table
                $tables[] = new TableDefinition(
                    name: $tableName,
                    columns: $columns
                );
            }
        }

        return [
            'tables' => $tables,
            'pivots' => $pivots,
        ];
    }

    /**
     * @return array<int, array{from: string, to: string, label: string}>
     */
    private function extractRelationships(string $dsl): array
    {
        $relationships = [];
        // Match: A ||--o{ B : "label"
        if (preg_match_all('/([a-zA-Z0-9_]+)\s*\|\|--o\{\s*([a-zA-Z0-9_]+)\s*:\s*"(.*?)"/', $dsl, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $relationships[] = [
                    'from' => $match[1],
                    'to' => $match[2],
                    'label' => $match[3],
                ];
            }
        }

        return $relationships;
    }

    /**
     * @return array<string, array<int, ColumnDefinition>>
     */
    private function extractTables(string $dsl): array
    {
        $tables = [];
        // Match: TableName { ... }
        if (preg_match_all('/([a-zA-Z0-9_]+)\s*\{\s*([^}]+)\s*\}/', $dsl, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $tableName = $match[1];
                $inner = $match[2];
                $tables[$tableName] = $this->parseColumns($tableName, $inner, $dsl);
            }
        }

        return $tables;
    }

    /**
     * @return array<int, ColumnDefinition>
     */
    private function parseColumns(string $tableName, string $inner, string $fullDsl): array
    {
        $columns = [];
        $lines = explode("\n", $inner);
        $position = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Mermaid format: type name modifiers
            // Example: bigint id PK
            // Example: varchar name
            $parts = preg_split('/\s+/', $line, 3);
            if (count($parts) < 2) {
                continue;
            }

            $type = $parts[0];
            $name = $parts[1];
            $modifiers = isset($parts[2]) ? explode(',', str_replace(' ', '', $parts[2])) : [];

            $isPrimary = in_array('PK', $modifiers);
            $indexType = $isPrimary ? IndexType::Primary : null;

            $isFk = in_array('FK', $modifiers);
            $fkTable = null;
            $fkColumn = null;

            if ($isFk) {
                // Find relationship pointing to this table to infer the FK target.
                // A ||--o{ B : "..." means B has FK to A.
                // We are parsing B's columns. So we look for a relationship where `to === tableName`.
                // However, the Mermaid syntax doesn't explicitly tie the FK column to the specific table in the DSL column definition.
                // Usually, the FK column is named `table_id`. So we can try to guess it, or look at the relationships.
                $rels = $this->extractRelationships($fullDsl);
                foreach ($rels as $rel) {
                    if ($rel['to'] === $tableName) {
                        $expectedFk = Str::singular($rel['from']).'_id';
                        if ($name === $expectedFk) {
                            $fkTable = $rel['from'];
                            $fkColumn = 'id'; // default assumption
                            break;
                        }
                    }
                }

                // Fallback to convention if no relationship lines are present
                if (! $fkTable && str_ends_with($name, '_id')) {
                    $fkTable = Str::plural(str_replace('_id', '', $name));
                    $fkColumn = 'id';
                }
            }

            $columns[] = new ColumnDefinition(
                name: $name,
                type: $this->mapMermaidType($type),
                isPrimary: $isPrimary,
                isNullable: false, // Defaulting, hard to parse from Mermaid unless we add custom modifiers
                defaultValue: null,
                isUnsigned: false,
                length: null,
                position: $position++,
                indexType: $indexType,
                fkTable: $fkTable,
                fkColumn: $fkColumn,
            );
        }

        return $columns;
    }

    private function mapMermaidType(string $type): ColumnType
    {
        return match (strtolower($type)) {
            'bigint' => ColumnType::BigInteger,
            'bool', 'boolean' => ColumnType::Boolean,
            'date' => ColumnType::Date,
            'datetime' => ColumnType::DateTime,
            'decimal' => ColumnType::Decimal,
            'float' => ColumnType::Float,
            'int', 'integer' => ColumnType::Integer,
            'json' => ColumnType::Json,
            'smallint' => ColumnType::SmallInteger,
            'varchar', 'string' => ColumnType::String,
            'text' => ColumnType::Text,
            'timestamp' => ColumnType::Timestamp,
            'ubigint' => ColumnType::UnsignedBigInteger,
            default => ColumnType::String,
        };
    }
}
