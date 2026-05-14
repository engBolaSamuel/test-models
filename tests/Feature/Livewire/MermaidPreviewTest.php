<?php

use App\Livewire\MermaidPreview;
use App\Models\Project;
use App\Models\ProjectTable;
use App\Models\User;
use Livewire\Livewire;

it('renders for authenticated project owner', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(MermaidPreview::class, ['project' => $project])
        ->assertOk()
        ->assertSee('ER Diagram');
});

it('starts with empty mermaidDsl (no diagram on load)', function () {
    $project = Project::factory()->create();
    ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'users']);

    Livewire::test(MermaidPreview::class, ['project' => $project])
        ->assertSet('mermaidDsl', '');
});

it('generates diagram on generateDiagram call', function () {
    $project = Project::factory()->create();
    ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'users']);

    Livewire::test(MermaidPreview::class, ['project' => $project])
        ->call('generateDiagram')
        ->assertSet('mermaidDsl', function ($value) {
            return str_contains($value, 'erDiagram') && str_contains($value, 'users {');
        });
});

it('shows empty state before button is clicked', function () {
    $project = Project::factory()->create();

    Livewire::test(MermaidPreview::class, ['project' => $project])
        ->assertSee('Click "View DB as ERD" to generate the diagram', false);
});

it('re-renders on schema-updated event', function () {
    $project = Project::factory()->create();
    ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'users']);

    Livewire::test(MermaidPreview::class, ['project' => $project])
        ->dispatch('schema-updated', projectId: $project->id)
        ->assertSet('mermaidDsl', function ($value) {
            return str_contains($value, 'erDiagram') && str_contains($value, 'users {');
        });
})->todo();
