<?php

namespace App\DTOs;

readonly class TableDefinition
{
    /**
     * @param  array<int, ColumnDefinition>  $columns
     */
    public function __construct(
        public string $name,
        public array $columns = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'columns' => array_map(
                fn (ColumnDefinition $column) => $column->toArray(),
                $this->columns,
            ),
        ];
    }
}
