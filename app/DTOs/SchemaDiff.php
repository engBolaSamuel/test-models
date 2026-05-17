<?php

namespace App\DTOs;

readonly class SchemaDiff
{
    /**
     * @param  array<int, TableDefinition>  $tablesToCreate
     * @param  array<int, string>  $tablesToDelete
     * @param  array<string, array<int, ColumnDefinition>>  $columnsToCreate
     * @param  array<string, array<int, ColumnDefinition>>  $columnsToUpdate
     * @param  array<string, array<int, string>>  $columnsToDelete
     * @param  array<int, PivotDefinition>  $pivotsToCreate
     * @param  array<int, string>  $pivotsToDelete
     */
    public function __construct(
        public array $tablesToCreate = [],
        public array $tablesToDelete = [],

        public array $columnsToCreate = [],
        public array $columnsToUpdate = [],
        public array $columnsToDelete = [],

        public array $pivotsToCreate = [],
        public array $pivotsToDelete = [],
    ) {}
}
