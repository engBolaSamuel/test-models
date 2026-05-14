<?php

use App\Enums\ColumnType;

it('has the expected number of cases', function () {
    expect(ColumnType::cases())->toHaveCount(13);
});

it('can be created from a valid value', function (string $value) {
    expect(ColumnType::from($value))->toBeInstanceOf(ColumnType::class);
})->with([
    'bigInteger',
    'boolean',
    'date',
    'dateTime',
    'decimal',
    'float',
    'integer',
    'json',
    'smallInteger',
    'string',
    'text',
    'timestamp',
    'unsignedBigInteger',
]);

it('returns a human-readable label', function () {
    expect(ColumnType::String->label())->toBe('String')
        ->and(ColumnType::BigInteger->label())->toBe('Big Integer')
        ->and(ColumnType::Json->label())->toBe('JSON')
        ->and(ColumnType::UnsignedBigInteger->label())->toBe('Unsigned Big Integer');
});

it('throws an error for an invalid value', function () {
    ColumnType::from('invalid');
})->throws(ValueError::class);
