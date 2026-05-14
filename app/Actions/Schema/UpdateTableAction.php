<?php

namespace App\Actions\Schema;

use App\Models\ProjectTable;

class UpdateTableAction
{
    /**
     * @param  array{name: string}  $data
     */
    public function execute(ProjectTable $table, array $data): ProjectTable
    {
        $table->update($data);

        return $table;
    }
}
