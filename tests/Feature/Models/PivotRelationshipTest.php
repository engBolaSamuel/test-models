<?php

use App\Models\PivotRelationship;
use App\Models\Project;
use App\Models\ProjectTable;

it('belongs to a project', function () {
    $pivot = PivotRelationship::factory()->create();

    expect($pivot->project)->toBeInstanceOf(Project::class);
});

it('belongs to table one', function () {
    $pivot = PivotRelationship::factory()->create();

    expect($pivot->tableOne)->toBeInstanceOf(ProjectTable::class);
});

it('belongs to table two', function () {
    $pivot = PivotRelationship::factory()->create();

    expect($pivot->tableTwo)->toBeInstanceOf(ProjectTable::class);
});

it('references two different tables', function () {
    $pivot = PivotRelationship::factory()->create();

    expect($pivot->tableOne->id)->not->toBe($pivot->tableTwo->id);
});

it('casts with_timestamps to boolean', function () {
    $pivot = PivotRelationship::factory()->create(['with_timestamps' => true]);

    expect($pivot->with_timestamps)->toBeTrue()->toBeBool();
});

it('supports the withoutTimestamps factory state', function () {
    $pivot = PivotRelationship::factory()->withoutTimestamps()->create();

    expect($pivot->with_timestamps)->toBeFalse();
});

it('is fillable with the correct attributes', function () {
    $project = Project::factory()->create();
    $tableOne = ProjectTable::factory()->create(['project_id' => $project->id]);
    $tableTwo = ProjectTable::factory()->create(['project_id' => $project->id]);

    $pivot = PivotRelationship::create([
        'project_id' => $project->id,
        'table_one_id' => $tableOne->id,
        'table_two_id' => $tableTwo->id,
        'pivot_table_name' => 'role_user',
        'with_timestamps' => false,
    ]);

    expect($pivot->pivot_table_name)->toBe('role_user')
        ->and($pivot->with_timestamps)->toBeFalse()
        ->and($pivot->project_id)->toBe($project->id);
});
