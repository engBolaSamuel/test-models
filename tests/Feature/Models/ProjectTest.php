<?php

use App\Models\PivotRelationship;
use App\Models\Project;
use App\Models\ProjectTable;
use App\Models\User;

it('belongs to a user', function () {
    $project = Project::factory()->create();

    expect($project->user)->toBeInstanceOf(User::class);
});

it('has many tables', function () {
    $project = Project::factory()->create();
    ProjectTable::factory()->count(3)->create(['project_id' => $project->id]);

    expect($project->tables)->toHaveCount(3)
        ->each->toBeInstanceOf(ProjectTable::class);
});

it('has many pivot relationships', function () {
    $project = Project::factory()->create();
    $tableOne = ProjectTable::factory()->create(['project_id' => $project->id]);
    $tableTwo = ProjectTable::factory()->create(['project_id' => $project->id]);

    PivotRelationship::factory()->create([
        'project_id' => $project->id,
        'table_one_id' => $tableOne->id,
        'table_two_id' => $tableTwo->id,
    ]);

    expect($project->pivotRelationships)->toHaveCount(1)
        ->first()->toBeInstanceOf(PivotRelationship::class);
});

it('is fillable with the correct attributes', function () {
    $user = User::factory()->create();

    $project = Project::create([
        'user_id' => $user->id,
        'name' => 'Test Project',
        'description' => 'A test description',
    ]);

    expect($project->name)->toBe('Test Project')
        ->and($project->description)->toBe('A test description')
        ->and($project->user_id)->toBe($user->id);
});
