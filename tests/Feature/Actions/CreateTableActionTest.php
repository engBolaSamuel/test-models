<?php

use App\Actions\Schema\CreateTableAction;
use App\Models\Project;

it('creates a table for a project', function () {
    $project = Project::factory()->create();
    $action = new CreateTableAction;

    $table = $action->execute($project, ['name' => 'users']);

    expect($table)
        ->name->toBe('users')
        ->project_id->toBe($project->id);

    $this->assertDatabaseHas('project_tables', [
        'project_id' => $project->id,
        'name' => 'users',
    ]);
});

it('creates multiple tables for the same project', function () {
    $project = Project::factory()->create();
    $action = new CreateTableAction;

    $action->execute($project, ['name' => 'users']);
    $action->execute($project, ['name' => 'posts']);

    expect($project->tables)->toHaveCount(2);
});
