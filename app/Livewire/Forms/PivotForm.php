<?php

namespace App\Livewire\Forms;

use App\Models\PivotRelationship;
use Livewire\Form;

class PivotForm extends Form
{
    public ?int $table_one_id = null;

    public ?int $table_two_id = null;

    public string $pivot_table_name = '';

    public bool $with_timestamps = true;

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'table_one_id' => ['required', 'integer', 'exists:project_tables,id'],
            'table_two_id' => ['required', 'integer', 'exists:project_tables,id', 'different:table_one_id'],
            'pivot_table_name' => ['nullable', 'string', 'max:255'],
            'with_timestamps' => ['boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'table_one_id' => $this->table_one_id,
            'table_two_id' => $this->table_two_id,
            'pivot_table_name' => $this->pivot_table_name,
            'with_timestamps' => $this->with_timestamps,
        ];
    }

    public function setFromPivot(PivotRelationship $pivot): void
    {
        $this->table_one_id = $pivot->table_one_id;
        $this->table_two_id = $pivot->table_two_id;
        $this->pivot_table_name = $pivot->pivot_table_name;
        $this->with_timestamps = $pivot->with_timestamps;
    }
}
