<?php

use App\Models\Project;
use App\Models\ProjectTable;
use App\Models\TableColumn;

it('belongs to a project', function () {
    $table = ProjectTable::factory()->create();

    expect($table->project)->toBeInstanceOf(Project::class);
});

it('has many columns', function () {
    $table = ProjectTable::factory()->create();
    TableColumn::factory()->count(5)->create(['project_table_id' => $table->id]);

    expect($table->columns)->toHaveCount(5)
        ->each->toBeInstanceOf(TableColumn::class);
});

it('orders columns by position', function () {
    $table = ProjectTable::factory()->create();

    TableColumn::factory()->create(['project_table_id' => $table->id, 'position' => 3, 'name' => 'third']);
    TableColumn::factory()->create(['project_table_id' => $table->id, 'position' => 1, 'name' => 'first']);
    TableColumn::factory()->create(['project_table_id' => $table->id, 'position' => 2, 'name' => 'second']);

    $names = $table->columns->pluck('name')->toArray();

    expect($names)->toBe(['first', 'second', 'third']);
});

it('is fillable with the correct attributes', function () {
    $project = Project::factory()->create();

    $table = ProjectTable::create([
        'project_id' => $project->id,
        'name' => 'users',
    ]);

    expect($table->name)->toBe('users')
        ->and($table->project_id)->toBe($project->id);
});
