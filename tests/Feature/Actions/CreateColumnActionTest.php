<?php

use App\Actions\Schema\CreateColumnAction;
use App\Enums\ColumnType;
use App\Models\ProjectTable;

it('creates a column for a table', function () {
    $table = ProjectTable::factory()->create();
    $action = new CreateColumnAction;

    $column = $action->execute($table, [
        'name' => 'email',
        'type' => ColumnType::String->value,
    ]);

    expect($column)
        ->name->toBe('email')
        ->type->toBe(ColumnType::String)
        ->project_table_id->toBe($table->id);
});

it('auto-assigns position when not provided', function () {
    $table = ProjectTable::factory()->create();
    $action = new CreateColumnAction;

    $first = $action->execute($table, ['name' => 'id', 'type' => ColumnType::BigInteger->value]);
    $second = $action->execute($table, ['name' => 'name', 'type' => ColumnType::String->value]);

    expect($first->position)->toBe(0);
    expect($second->position)->toBe(1);
});

it('respects an explicitly provided position', function () {
    $table = ProjectTable::factory()->create();
    $action = new CreateColumnAction;

    $column = $action->execute($table, [
        'name' => 'email',
        'type' => ColumnType::String->value,
        'position' => 5,
    ]);

    expect($column->position)->toBe(5);
});
