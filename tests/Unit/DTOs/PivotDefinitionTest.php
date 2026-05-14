<?php

use App\DTOs\PivotDefinition;

it('can be constructed with defaults', function () {
    $pivot = new PivotDefinition(
        tableOne: 'users',
        tableTwo: 'roles',
        pivotTableName: 'role_user',
    );

    expect($pivot->tableOne)->toBe('users')
        ->and($pivot->tableTwo)->toBe('roles')
        ->and($pivot->pivotTableName)->toBe('role_user')
        ->and($pivot->withTimestamps)->toBeTrue();
});

it('can disable timestamps', function () {
    $pivot = new PivotDefinition(
        tableOne: 'posts',
        tableTwo: 'tags',
        pivotTableName: 'post_tag',
        withTimestamps: false,
    );

    expect($pivot->withTimestamps)->toBeFalse();
});

it('converts to array', function () {
    $pivot = new PivotDefinition(
        tableOne: 'users',
        tableTwo: 'roles',
        pivotTableName: 'role_user',
        withTimestamps: true,
    );

    $array = $pivot->toArray();

    expect($array)->toBe([
        'table_one' => 'users',
        'table_two' => 'roles',
        'pivot_table_name' => 'role_user',
        'with_timestamps' => true,
    ]);
});

it('is immutable', function () {
    $pivot = new PivotDefinition(
        tableOne: 'users',
        tableTwo: 'roles',
        pivotTableName: 'role_user',
    );

    expect(fn () => $pivot->tableOne = 'changed')->toThrow(Error::class);
});
