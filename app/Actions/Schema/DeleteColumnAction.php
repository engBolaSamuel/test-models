<?php

namespace App\Actions\Schema;

use App\Models\TableColumn;

class DeleteColumnAction
{
    public function execute(TableColumn $column): void
    {
        $column->delete();
    }
}
