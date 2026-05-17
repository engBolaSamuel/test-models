<?php

namespace App\Services;

use App\Actions\Mermaid\ParseMermaidAction;
use App\Actions\Schema\CreateColumnAction;
use App\Actions\Schema\CreatePivotRelationshipAction;
use App\Actions\Schema\CreateTableAction;
use App\Actions\Schema\DeleteColumnAction;
use App\Actions\Schema\DeletePivotRelationshipAction;
use App\Actions\Schema\DeleteTableAction;
use App\Actions\Schema\UpdateColumnAction;
use App\DTOs\PivotDefinition;
use App\DTOs\SchemaDiff;
use App\DTOs\TableDefinition;
use App\Models\Project;
use App\Models\ProjectTable;
use Illuminate\Support\Facades\DB;

class SchemaSyncService
{
    public function __construct(
        protected ParseMermaidAction $parseAction,
        protected CreateTableAction $createTableAction,
        protected DeleteTableAction $deleteTableAction,
        protected CreateColumnAction $createColumnAction,
        protected UpdateColumnAction $updateColumnAction,
        protected DeleteColumnAction $deleteColumnAction,
        protected CreatePivotRelationshipAction $createPivotAction,
        protected DeletePivotRelationshipAction $deletePivotAction
    ) {}

    public function diffAndApply(Project $project, string $dsl): SchemaDiff
    {
        $parsed = $this->parseAction->execute($dsl);

        $diff = $this->computeDiff($project, $parsed['tables'], $parsed['pivots']);

        $this->applyDiff($project, $diff);

        return $diff;
    }

    /**
     * @param  array<int, TableDefinition>  $parsedTables
     * @param  array<int, PivotDefinition>  $parsedPivots
     */
    protected function computeDiff(Project $project, array $parsedTables, array $parsedPivots): SchemaDiff
    {
        $project->load(['tables.columns', 'pivotRelationships']);

        $dbTables = $project->tables->keyBy('name');

        $tablesToCreate = [];
        $tablesToDelete = [];
        $columnsToCreate = [];
        $columnsToUpdate = [];
        $columnsToDelete = [];

        // Identify Tables to Create or Update
        foreach ($parsedTables as $parsedTable) {
            if (! $dbTables->has($parsedTable->name)) {
                $tablesToCreate[] = $parsedTable;
                $columnsToCreate[$parsedTable->name] = $parsedTable->columns;
            } else {
                // Table exists, compute column diffs
                /** @var ProjectTable $dbTable */
                $dbTable = $dbTables->get($parsedTable->name);
                $dbColumns = $dbTable->columns->keyBy('name');

                $parsedColNames = [];
                foreach ($parsedTable->columns as $parsedColumn) {
                    $parsedColNames[] = $parsedColumn->name;
                    if (! $dbColumns->has($parsedColumn->name)) {
                        $columnsToCreate[$parsedTable->name][] = $parsedColumn;
                    } else {
                        // Compare columns (simplified, assuming we just update all properties)
                        // In reality, we might want to check if they are actually different
                        $columnsToUpdate[$parsedTable->name][] = $parsedColumn;
                    }
                }

                // Identify Columns to Delete
                foreach ($dbColumns as $colName => $dbColumn) {
                    if (! in_array($colName, $parsedColNames)) {
                        $columnsToDelete[$parsedTable->name][] = $colName;
                    }
                }
            }
        }

        // Identify Tables to Delete
        $parsedTableNames = collect($parsedTables)->pluck('name')->toArray();
        foreach ($dbTables as $tableName => $dbTable) {
            if (! in_array($tableName, $parsedTableNames)) {
                $tablesToDelete[] = $tableName;
            }
        }

        // Identify Pivots to Create or Delete
        $dbPivots = $project->pivotRelationships->keyBy('pivot_table_name');
        $pivotsToCreate = [];
        $pivotsToDelete = [];

        $parsedPivotNames = [];
        foreach ($parsedPivots as $parsedPivot) {
            $parsedPivotNames[] = $parsedPivot->pivotTableName;
            if (! $dbPivots->has($parsedPivot->pivotTableName)) {
                $pivotsToCreate[] = $parsedPivot;
            }
        }

        foreach ($dbPivots as $pivotName => $dbPivot) {
            if (! in_array($pivotName, $parsedPivotNames)) {
                $pivotsToDelete[] = $pivotName;
            }
        }

        return new SchemaDiff(
            tablesToCreate: $tablesToCreate,
            tablesToDelete: $tablesToDelete,
            columnsToCreate: $columnsToCreate,
            columnsToUpdate: $columnsToUpdate,
            columnsToDelete: $columnsToDelete,
            pivotsToCreate: $pivotsToCreate,
            pivotsToDelete: $pivotsToDelete,
        );
    }

    protected function applyDiff(Project $project, SchemaDiff $diff): void
    {
        DB::transaction(function () use ($project, $diff) {
            // Delete Pivots
            $dbPivots = $project->pivotRelationships()->whereIn('pivot_table_name', $diff->pivotsToDelete)->get();
            foreach ($dbPivots as $pivot) {
                $this->deletePivotAction->execute($pivot);
            }

            // Delete Tables (this deletes columns too due to cascade or actions)
            $dbTables = $project->tables()->whereIn('name', $diff->tablesToDelete)->get();
            foreach ($dbTables as $table) {
                $this->deleteTableAction->execute($table);
            }

            // Create Tables
            foreach ($diff->tablesToCreate as $tableDef) {
                $this->createTableAction->execute($project, ['name' => $tableDef->name]);
            }

            // Refresh project tables for column and pivot creations
            $project->load('tables.columns');
            $tablesMap = $project->tables->keyBy('name');

            // Delete Columns
            foreach ($diff->columnsToDelete as $tableName => $colNames) {
                if ($tablesMap->has($tableName)) {
                    $table = $tablesMap->get($tableName);
                    $cols = $table->columns()->whereIn('name', $colNames)->get();
                    foreach ($cols as $col) {
                        $this->deleteColumnAction->execute($col);
                    }
                }
            }

            // Create Columns
            foreach ($diff->columnsToCreate as $tableName => $colDefs) {
                if ($tablesMap->has($tableName)) {
                    $table = $tablesMap->get($tableName);
                    foreach ($colDefs as $colDef) {
                        $this->createColumnAction->execute($table, $colDef->toArray());
                    }
                }
            }

            // Update Columns
            foreach ($diff->columnsToUpdate as $tableName => $colDefs) {
                if ($tablesMap->has($tableName)) {
                    $table = $tablesMap->get($tableName);
                    $colsMap = $table->columns->keyBy('name');
                    foreach ($colDefs as $colDef) {
                        if ($colsMap->has($colDef->name)) {
                            $this->updateColumnAction->execute($colsMap->get($colDef->name), $colDef->toArray());
                        }
                    }
                }
            }

            // Create Pivots
            // Note: If the pivot's tables were just created, they exist in tablesMap
            foreach ($diff->pivotsToCreate as $pivotDef) {
                $t1 = $tablesMap->get($pivotDef->tableOne);
                $t2 = $tablesMap->get($pivotDef->tableTwo);

                if ($t1 && $t2) {
                    $this->createPivotAction->execute($project, [
                        'table_one_id' => $t1->id,
                        'table_two_id' => $t2->id,
                        'pivot_table_name' => $pivotDef->pivotTableName,
                        'with_timestamps' => $pivotDef->withTimestamps,
                    ]);
                }
            }
        });
    }
}
