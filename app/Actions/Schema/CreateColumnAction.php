<?php

namespace App\Actions\Schema;

use App\Models\ProjectTable;
use App\Models\TableColumn;

class CreateColumnAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(ProjectTable $table, array $data): TableColumn
    {
        if (! isset($data['position'])) {
            $data['position'] = ($table->columns()->max('position') ?? -1) + 1;
        }

        return $table->columns()->create($data);
    }
}
