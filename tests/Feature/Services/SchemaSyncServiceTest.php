<?php

use App\Actions\Mermaid\ParseMermaidAction;
use App\Models\Project;
use App\Models\ProjectTable;
use App\Services\SchemaSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('syncs mermaid dsl to database', function () {
    $project = Project::factory()->create();

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

    $service = app(SchemaSyncService::class);
    $diff = $service->diffAndApply($project, $dsl);

    expect($diff->tablesToCreate)->toHaveCount(2)
        ->and($diff->pivotsToCreate)->toHaveCount(1);

    $project->refresh();
    $project->load(['tables.columns', 'pivotRelationships']);

    expect($project->tables)->toHaveCount(2)
        ->and($project->pivotRelationships)->toHaveCount(1);

    /** @var ProjectTable $usersTable */
    $usersTable = $project->tables->firstWhere('name', 'users');
    expect($usersTable->columns)->toHaveCount(3);
    
    $rolesTable = $project->tables->firstWhere('name', 'roles');
    expect($rolesTable->columns)->toHaveCount(2);

    $pivot = $project->pivotRelationships->first();
    expect($pivot->pivot_table_name)->toBe('role_user')
        ->and($pivot->table_one_id)->toBe($usersTable->id)
        ->and($pivot->table_two_id)->toBe($rolesTable->id);
});
