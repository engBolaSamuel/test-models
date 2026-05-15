<?php

use App\Actions\Mermaid\ParseMermaidAction;
use App\DTOs\ColumnDefinition;
use App\DTOs\PivotDefinition;
use App\DTOs\TableDefinition;
use App\Enums\ColumnType;
use App\Enums\IndexType;

it('parses mermaid ER diagram into tables and pivots', function () {
    $dsl = <<<EOF
erDiagram
    users {
        bigint id PK
        varchar name
        varchar email
    }
    roles {
        bigint id PK
        varchar title
    }
    role_user {
        bigint role_id FK
        bigint user_id FK
        timestamp created_at
        timestamp updated_at
    }
    users ||--o{ role_user : "has"
    roles ||--o{ role_user : "has"
EOF;

    $action = new ParseMermaidAction();
    $result = $action->execute($dsl);

    expect($result['tables'])->toHaveCount(2)
        ->and($result['pivots'])->toHaveCount(1);

    /** @var TableDefinition $usersTable */
    $usersTable = collect($result['tables'])->firstWhere('name', 'users');
    expect($usersTable)->not->toBeNull()
        ->and($usersTable->columns)->toHaveCount(3)
        ->and($usersTable->columns[0]->name)->toBe('id')
        ->and($usersTable->columns[0]->type)->toBe(ColumnType::BigInteger)
        ->and($usersTable->columns[0]->isPrimary)->toBeTrue()
        ->and($usersTable->columns[0]->indexType)->toBe(IndexType::Primary)
        ->and($usersTable->columns[1]->name)->toBe('name')
        ->and($usersTable->columns[1]->type)->toBe(ColumnType::String)
        ->and($usersTable->columns[1]->isPrimary)->toBeFalse();

    /** @var PivotDefinition $pivot */
    $pivot = $result['pivots'][0];
    expect($pivot->pivotTableName)->toBe('role_user')
        ->and($pivot->tableOne)->toBe('users')
        ->and($pivot->tableTwo)->toBe('roles')
        ->and($pivot->withTimestamps)->toBeTrue();
});
