<?php

namespace App\Actions\Schema;

use App\Models\TableColumn;

class UpdateColumnAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(TableColumn $column, array $data): TableColumn
    {
        $column->update($data);

        return $column;
    }
}
