<?php

use App\Livewire\MermaidEditor;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders the component', function () {
    $project = Project::factory()->create();

    Livewire::test(MermaidEditor::class, ['project' => $project])
        ->assertStatus(200);
});

it('applies dsl and triggers schema update', function () {
    $project = Project::factory()->create();

    $dsl = <<<'EOF'
erDiagram
    users {
        bigint id PK
    }
EOF;

    Livewire::test(MermaidEditor::class, ['project' => $project])
        ->set('dsl', $dsl)
        ->call('apply')
        ->assertDispatched('mermaid-applied')
        ->assertDispatched('schema-updated')
        ->assertSet('errorMessage', null)
        ->assertSet('isDirty', false);

    $project->refresh();
    expect($project->tables)->toHaveCount(1)
        ->and($project->tables->first()->name)->toBe('users');
});
