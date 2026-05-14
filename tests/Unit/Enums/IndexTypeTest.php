<?php

use App\Enums\IndexType;

it('has the expected number of cases', function () {
    expect(IndexType::cases())->toHaveCount(4);
});

it('can be created from a valid value', function (string $value) {
    expect(IndexType::from($value))->toBeInstanceOf(IndexType::class);
})->with([
    'none',
    'primary',
    'unique',
    'index',
]);

it('returns a human-readable label', function () {
    expect(IndexType::None->label())->toBe('None')
        ->and(IndexType::Primary->label())->toBe('Primary')
        ->and(IndexType::Unique->label())->toBe('Unique')
        ->and(IndexType::Index->label())->toBe('Index');
});

it('throws an error for an invalid value', function () {
    IndexType::from('invalid');
})->throws(ValueError::class);
