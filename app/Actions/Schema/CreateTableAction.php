<?php

namespace App\Actions\Schema;

use App\Models\Project;
use App\Models\ProjectTable;

class CreateTableAction
{
    /**
     * @param  array{name: string}  $data
     */
    public function execute(Project $project, array $data): ProjectTable
    {
        return $project->tables()->create($data);
    }
}
