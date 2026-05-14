<?php

use App\Actions\Mermaid\GenerateMermaidAction;
use App\Enums\ColumnType;
use App\Enums\IndexType;
use App\Models\PivotRelationship;
use App\Models\Project;
use App\Models\ProjectTable;
use App\Models\TableColumn;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns empty string for project with no tables', function () {
    $project = Project::factory()->create();

    $action = new GenerateMermaidAction;
    $dsl = $action->execute($project);

    expect($dsl)->toBe('');
});

it('generates entity block for a single table with columns', function () {
    $project = Project::factory()->create();
    $table = ProjectTable::factory()->create([
        'project_id' => $project->id,
        'name' => 'users',
    ]);
    TableColumn::factory()->create([
        'project_table_id' => $table->id,
        'name' => 'id',
        'type' => ColumnType::BigInteger,
    ]);
    TableColumn::factory()->create([
        'project_table_id' => $table->id,
        'name' => 'name',
        'type' => ColumnType::String,
    ]);

    $action = new GenerateMermaidAction;
    $dsl = $action->execute($project);

    expect($dsl)->toContain('erDiagram')
        ->toContain('users {')
        ->toContain('bigint id')
        ->toContain('varchar name');
});

it('maps column types to correct Mermaid type labels', function () {
    $project = Project::factory()->create();
    $table = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'test_types']);
    
    $types = [
        ColumnType::BigInteger->value => 'bigint',
        ColumnType::Boolean->value => 'bool',
        ColumnType::Date->value => 'date',
        ColumnType::DateTime->value => 'datetime',
        ColumnType::Decimal->value => 'decimal',
        ColumnType::Float->value => 'float',
        ColumnType::Integer->value => 'int',
        ColumnType::Json->value => 'json',
        ColumnType::SmallInteger->value => 'smallint',
        ColumnType::String->value => 'varchar',
        ColumnType::Text->value => 'text',
        ColumnType::Timestamp->value => 'timestamp',
        ColumnType::UnsignedBigInteger->value => 'ubigint',
    ];

    foreach ($types as $enumType => $mermaidLabel) {
        TableColumn::factory()->create([
            'project_table_id' => $table->id,
            'name' => 'col_' . $mermaidLabel,
            'type' => ColumnType::from($enumType),
        ]);
    }

    $action = new GenerateMermaidAction;
    $dsl = $action->execute($project);

    foreach ($types as $enumType => $mermaidLabel) {
        expect($dsl)->toContain("{$mermaidLabel} col_{$mermaidLabel}");
    }
});

it('marks primary key columns with PK', function () {
    $project = Project::factory()->create();
    $table = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'users']);
    TableColumn::factory()->create([
        'project_table_id' => $table->id,
        'name' => 'id',
        'type' => ColumnType::BigInteger,
        'index_type' => IndexType::Primary,
    ]);

    $action = new GenerateMermaidAction;
    $dsl = $action->execute($project);

    expect($dsl)->toContain('bigint id PK');
});

it('marks foreign key columns with FK', function () {
    $project = Project::factory()->create();
    $table = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'posts']);
    TableColumn::factory()->create([
        'project_table_id' => $table->id,
        'name' => 'user_id',
        'type' => ColumnType::BigInteger,
        'fk_table' => 'users',
        'fk_column' => 'id',
    ]);

    $action = new GenerateMermaidAction;
    $dsl = $action->execute($project);

    expect($dsl)->toContain('bigint user_id FK');
});

it('generates FK relationship lines for columns with fk_table', function () {
    $project = Project::factory()->create();
    $users = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'users']);
    $posts = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'posts']);
    
    TableColumn::factory()->create([
        'project_table_id' => $posts->id,
        'name' => 'user_id',
        'type' => ColumnType::BigInteger,
        'fk_table' => 'users',
        'fk_column' => 'id',
    ]);

    $action = new GenerateMermaidAction;
    $dsl = $action->execute($project);

    expect($dsl)->toContain('users ||--o{ posts : "user_id"');
});

it('generates M2M relationship lines for pivot relationships', function () {
    $project = Project::factory()->create();
    $posts = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'posts']);
    $tags = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'tags']);
    
    PivotRelationship::factory()->create([
        'project_id' => $project->id,
        'table_one_id' => $posts->id,
        'table_two_id' => $tags->id,
        'pivot_table_name' => 'post_tag',
        'with_timestamps' => true,
    ]);

    $action = new GenerateMermaidAction;
    $dsl = $action->execute($project);

    expect($dsl)->toContain('post_tag {')
        ->toContain('bigint post_id FK')
        ->toContain('bigint tag_id FK')
        ->toContain('timestamp created_at')
        ->toContain('timestamp updated_at')
        ->toContain('posts ||--o{ post_tag : "has"')
        ->toContain('tags ||--o{ post_tag : "has"');
});

it('generates complete DSL with tables, FKs, and pivots', function () {
    $project = Project::factory()->create();
    $users = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'users']);
    $posts = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'posts']);
    $tags = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'tags']);
    
    TableColumn::factory()->create([
        'project_table_id' => $posts->id,
        'name' => 'user_id',
        'type' => ColumnType::BigInteger,
        'fk_table' => 'users',
        'fk_column' => 'id',
    ]);
    
    PivotRelationship::factory()->create([
        'project_id' => $project->id,
        'table_one_id' => $posts->id,
        'table_two_id' => $tags->id,
        'pivot_table_name' => 'post_tag',
        'with_timestamps' => false,
    ]);

    $action = new GenerateMermaidAction;
    $dsl = $action->execute($project);

    expect($dsl)->toContain('users {')
        ->toContain('posts {')
        ->toContain('tags {')
        ->toContain('users ||--o{ posts : "user_id"')
        ->toContain('post_tag {')
        ->toContain('posts ||--o{ post_tag : "has"')
        ->toContain('tags ||--o{ post_tag : "has"');
});
