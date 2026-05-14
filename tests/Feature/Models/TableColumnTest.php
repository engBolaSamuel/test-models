<?php

use App\Enums\ColumnType;
use App\Enums\IndexType;
use App\Models\ProjectTable;
use App\Models\TableColumn;

it('belongs to a project table', function () {
    $column = TableColumn::factory()->create();

    expect($column->projectTable)->toBeInstanceOf(ProjectTable::class);
});

it('casts type to ColumnType enum', function () {
    $column = TableColumn::factory()->create(['type' => ColumnType::Integer]);

    expect($column->type)->toBeInstanceOf(ColumnType::class)
        ->and($column->type)->toBe(ColumnType::Integer);
});

it('casts index_type to IndexType enum', function () {
    $column = TableColumn::factory()->create(['index_type' => IndexType::Unique]);

    expect($column->index_type)->toBeInstanceOf(IndexType::class)
        ->and($column->index_type)->toBe(IndexType::Unique);
});

it('casts boolean fields correctly', function () {
    $column = TableColumn::factory()->create([
        'is_primary' => true,
        'is_nullable' => true,
        'is_unsigned' => true,
    ]);

    expect($column->is_primary)->toBeTrue()
        ->and($column->is_nullable)->toBeTrue()
        ->and($column->is_unsigned)->toBeTrue();
});

it('casts integer fields correctly', function () {
    $column = TableColumn::factory()->create([
        'length' => 255,
        'position' => 5,
    ]);

    expect($column->length)->toBe(255)
        ->and($column->position)->toBe(5);
});

it('is fillable with all column attributes', function () {
    $table = ProjectTable::factory()->create();

    $column = TableColumn::create([
        'project_table_id' => $table->id,
        'name' => 'email',
        'type' => ColumnType::String,
        'is_primary' => false,
        'is_nullable' => true,
        'default_value' => null,
        'is_unsigned' => false,
        'length' => 255,
        'position' => 2,
        'index_type' => IndexType::Unique,
        'fk_table' => null,
        'fk_column' => null,
    ]);

    expect($column->name)->toBe('email')
        ->and($column->type)->toBe(ColumnType::String)
        ->and($column->is_nullable)->toBeTrue()
        ->and($column->length)->toBe(255)
        ->and($column->index_type)->toBe(IndexType::Unique);
});

it('supports foreign key attributes', function () {
    $column = TableColumn::factory()->foreignKey('users', 'id')->create();

    expect($column->fk_table)->toBe('users')
        ->and($column->fk_column)->toBe('id')
        ->and($column->type)->toBe(ColumnType::UnsignedBigInteger)
        ->and($column->index_type)->toBe(IndexType::Index);
});
