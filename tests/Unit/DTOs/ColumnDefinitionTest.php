<?php

use App\DTOs\ColumnDefinition;
use App\Enums\ColumnType;
use App\Enums\IndexType;

it('can be constructed with minimal arguments', function () {
    $column = new ColumnDefinition(
        name: 'email',
        type: ColumnType::String,
    );

    expect($column->name)->toBe('email')
        ->and($column->type)->toBe(ColumnType::String)
        ->and($column->isPrimary)->toBeFalse()
        ->and($column->isNullable)->toBeFalse()
        ->and($column->defaultValue)->toBeNull()
        ->and($column->isUnsigned)->toBeFalse()
        ->and($column->length)->toBeNull()
        ->and($column->position)->toBe(0)
        ->and($column->indexType)->toBeNull()
        ->and($column->fkTable)->toBeNull()
        ->and($column->fkColumn)->toBeNull();
});

it('can be constructed with all arguments', function () {
    $column = new ColumnDefinition(
        name: 'user_id',
        type: ColumnType::UnsignedBigInteger,
        isPrimary: false,
        isNullable: false,
        defaultValue: null,
        isUnsigned: true,
        length: null,
        position: 3,
        indexType: IndexType::Index,
        fkTable: 'users',
        fkColumn: 'id',
    );

    expect($column->name)->toBe('user_id')
        ->and($column->type)->toBe(ColumnType::UnsignedBigInteger)
        ->and($column->isUnsigned)->toBeTrue()
        ->and($column->position)->toBe(3)
        ->and($column->indexType)->toBe(IndexType::Index)
        ->and($column->fkTable)->toBe('users')
        ->and($column->fkColumn)->toBe('id');
});

it('converts to array', function () {
    $column = new ColumnDefinition(
        name: 'name',
        type: ColumnType::String,
        isNullable: true,
        length: 255,
        position: 1,
        indexType: IndexType::Unique,
    );

    $array = $column->toArray();

    expect($array)->toBeArray()
        ->and($array['name'])->toBe('name')
        ->and($array['type'])->toBe(ColumnType::String)
        ->and($array['is_nullable'])->toBeTrue()
        ->and($array['length'])->toBe(255)
        ->and($array['position'])->toBe(1)
        ->and($array['index_type'])->toBe(IndexType::Unique);
});

it('is immutable', function () {
    $column = new ColumnDefinition(name: 'id', type: ColumnType::BigInteger);

    expect(fn () => $column->name = 'changed')->toThrow(Error::class);
});
