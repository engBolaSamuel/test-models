<?php

namespace App\Actions\Schema;

use App\Models\ProjectTable;

class ReorderColumnsAction
{
    /**
     * @param  list<int>  $orderedColumnIds
     */
    public function execute(ProjectTable $table, array $orderedColumnIds): void
    {
        foreach ($orderedColumnIds as $position => $columnId) {
            $table->columns()
                ->where('id', $columnId)
                ->update(['position' => $position]);
        }
    }
}
