<?php

namespace App\Actions\Schema;

use App\Models\ProjectTable;

class DeleteTableAction
{
    public function execute(ProjectTable $table): void
    {
        $table->delete();
    }
}
