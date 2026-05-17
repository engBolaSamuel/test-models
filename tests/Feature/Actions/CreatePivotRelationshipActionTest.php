<?php

use App\Actions\Schema\CreatePivotRelationshipAction;
use App\Models\Project;
use App\Models\ProjectTable;

it('creates a pivot relationship between two tables', function () {
    $project = Project::factory()->create();
    $tableOne = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'users']);
    $tableTwo = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'roles']);

    $action = new CreatePivotRelationshipAction;
    $pivot = $action->execute($project, [
        'table_one_id' => $tableOne->id,
        'table_two_id' => $tableTwo->id,
        'pivot_table_name' => 'role_user',
        'with_timestamps' => true,
    ]);

    expect($pivot)
        ->project_id->toBe($project->id)
        ->table_one_id->toBe($tableOne->id)
        ->table_two_id->toBe($tableTwo->id)
        ->pivot_table_name->toBe('role_user')
        ->with_timestamps->toBeTrue();
});

it('auto-generates pivot table name when empty', function () {
    $project = Project::factory()->create();
    $tableOne = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'users']);
    $tableTwo = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'roles']);

    $action = new CreatePivotRelationshipAction;
    $pivot = $action->execute($project, [
        'table_one_id' => $tableOne->id,
        'table_two_id' => $tableTwo->id,
        'pivot_table_name' => '',
        'with_timestamps' => true,
    ]);

    expect($pivot->pivot_table_name)->toBe('role_user');
});
