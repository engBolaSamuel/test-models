<?php

namespace App\Livewire\Forms;

use App\Enums\ColumnType;
use App\Enums\IndexType;
use App\Models\TableColumn;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class ColumnForm extends Form
{
    public string $name = '';

    public string $type = 'string';

    public bool $is_nullable = false;

    public ?string $default_value = null;

    public bool $is_unsigned = false;

    public ?int $length = null;

    public ?string $index_type = null;

    public ?string $fk_table = null;

    public ?string $fk_column = null;

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', new Enum(ColumnType::class)],
            'is_nullable' => ['boolean'],
            'default_value' => ['nullable', 'string', 'max:255'],
            'is_unsigned' => ['boolean'],
            'length' => ['nullable', 'integer', 'min:1'],
            'index_type' => ['nullable', new Enum(IndexType::class)],
            'fk_table' => ['nullable', 'string', 'max:255'],
            'fk_column' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'is_nullable' => $this->is_nullable,
            'default_value' => $this->default_value,
            'is_unsigned' => $this->is_unsigned,
            'length' => $this->length,
            'index_type' => $this->index_type,
            'fk_table' => $this->fk_table,
            'fk_column' => $this->fk_column,
        ];
    }

    public function setFromColumn(TableColumn $column): void
    {
        $this->name = $column->name;
        $this->type = $column->type->value;
        $this->is_nullable = $column->is_nullable;
        $this->default_value = $column->default_value;
        $this->is_unsigned = $column->is_unsigned;
        $this->length = $column->length;
        $this->index_type = $column->index_type?->value;
        $this->fk_table = $column->fk_table;
        $this->fk_column = $column->fk_column;
    }
}
