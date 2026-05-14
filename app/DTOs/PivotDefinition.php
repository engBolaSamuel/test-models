<?php

namespace App\DTOs;

readonly class PivotDefinition
{
    public function __construct(
        public string $tableOne,
        public string $tableTwo,
        public string $pivotTableName,
        public bool $withTimestamps = true,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'table_one' => $this->tableOne,
            'table_two' => $this->tableTwo,
            'pivot_table_name' => $this->pivotTableName,
            'with_timestamps' => $this->withTimestamps,
        ];
    }
}
