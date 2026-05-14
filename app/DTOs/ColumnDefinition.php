<?php

namespace App\DTOs;

use App\Enums\ColumnType;
use App\Enums\IndexType;

readonly class ColumnDefinition
{
    public function __construct(
        public string $name,
        public ColumnType $type,
        public bool $isPrimary = false,
        public bool $isNullable = false,
        public ?string $defaultValue = null,
        public bool $isUnsigned = false,
        public ?int $length = null,
        public int $position = 0,
        public ?IndexType $indexType = null,
        public ?string $fkTable = null,
        public ?string $fkColumn = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'is_primary' => $this->isPrimary,
            'is_nullable' => $this->isNullable,
            'default_value' => $this->defaultValue,
            'is_unsigned' => $this->isUnsigned,
            'length' => $this->length,
            'position' => $this->position,
            'index_type' => $this->indexType,
            'fk_table' => $this->fkTable,
            'fk_column' => $this->fkColumn,
        ];
    }
}
