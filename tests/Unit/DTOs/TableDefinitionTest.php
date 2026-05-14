<?php

use App\DTOs\ColumnDefinition;
use App\DTOs\TableDefinition;
use App\Enums\ColumnType;

it('can be constructed with a name only', function () {
    $table = new TableDefinition(name: 'users');

    expect($table->name)->toBe('users')
        ->and($table->columns)->toBeArray()->toBeEmpty();
});

it('can be constructed with columns', function () {
    $columns = [
        new ColumnDefinition(name: 'id', type: ColumnType::UnsignedBigInteger, isPrimary: true),
        new ColumnDefinition(name: 'name', type: ColumnType::String),
    ];

    $table = new TableDefinition(name: 'users', columns: $columns);

    expect($table->columns)->toHaveCount(2)
        ->and($table->columns[0]->name)->toBe('id')
        ->and($table->columns[1]->name)->toBe('name');
});

it('converts to array including columns', function () {
    $table = new TableDefinition(
        name: 'posts',
        columns: [
            new ColumnDefinition(name: 'id', type: ColumnType::UnsignedBigInteger, isPrimary: true),
            new ColumnDefinition(name: 'title', type: ColumnType::String, length: 255),
        ],
    );

    $array = $table->toArray();

    expect($array['name'])->toBe('posts')
        ->and($array['columns'])->toHaveCount(2)
        ->and($array['columns'][0]['name'])->toBe('id')
        ->and($array['columns'][1]['name'])->toBe('title')
        ->and($array['columns'][1]['length'])->toBe(255);
});

it('is immutable', function () {
    $table = new TableDefinition(name: 'users');

    expect(fn () => $table->name = 'changed')->toThrow(Error::class);
});
