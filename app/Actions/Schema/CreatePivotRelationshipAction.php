<?php

namespace App\Actions\Schema;

use App\Models\PivotRelationship;
use App\Models\Project;

class CreatePivotRelationshipAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Project $project, array $data): PivotRelationship
    {
        $data['project_id'] = $project->id;

        if (empty($data['pivot_table_name'])) {
            $tableOne = $project->tables()->find($data['table_one_id']);
            $tableTwo = $project->tables()->find($data['table_two_id']);

            $names = [$tableOne->name, $tableTwo->name];
            sort($names);
            $data['pivot_table_name'] = implode('_', $names);
        }

        return PivotRelationship::create($data);
    }
}
