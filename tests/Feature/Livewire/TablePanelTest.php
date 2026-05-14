<?php

use App\Livewire\TablePanel;
use App\Models\Project;
use App\Models\ProjectTable;
use App\Models\User;
use Livewire\Livewire;

it('displays tables for a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'users']);
    ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'posts']);

    Livewire::actingAs($user)
        ->test(TablePanel::class, ['project' => $project])
        ->assertSee('users')
        ->assertSee('posts');
});

it('displays empty state when no tables exist', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(TablePanel::class, ['project' => $project])
        ->assertSee('No tables yet.');
});

it('can create a new table', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(TablePanel::class, ['project' => $project])
        ->set('form.name', 'users')
        ->call('createTable')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('project_tables', [
        'project_id' => $project->id,
        'name' => 'users',
    ]);
});

it('validates table name is required', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(TablePanel::class, ['project' => $project])
        ->set('form.name', '')
        ->call('createTable')
        ->assertHasErrors(['form.name' => 'required']);
});

it('can rename a table via inline editing', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $table = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'old_name']);

    Livewire::actingAs($user)
        ->test(TablePanel::class, ['project' => $project])
        ->call('startEditing', $table->id)
        ->assertSet('editingTableId', $table->id)
        ->assertSet('editingName', 'old_name')
        ->set('editingName', 'new_name')
        ->call('saveEdit')
        ->assertSet('editingTableId', null);

    $this->assertDatabaseHas('project_tables', [
        'id' => $table->id,
        'name' => 'new_name',
    ]);
});

it('can delete a table', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $table = ProjectTable::factory()->create(['project_id' => $project->id]);

    Livewire::actingAs($user)
        ->test(TablePanel::class, ['project' => $project])
        ->call('deleteTable', $table->id);

    $this->assertDatabaseMissing('project_tables', ['id' => $table->id]);
});

it('dispatches table-selected event when a table is clicked', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $table = ProjectTable::factory()->create(['project_id' => $project->id]);

    Livewire::actingAs($user)
        ->test(TablePanel::class, ['project' => $project])
        ->call('selectTable', $table->id)
        ->assertSet('selectedTableId', $table->id)
        ->assertDispatched('table-selected', tableId: $table->id);
});

it('clears selection when selected table is deleted', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $table = ProjectTable::factory()->create(['project_id' => $project->id]);

    Livewire::actingAs($user)
        ->test(TablePanel::class, ['project' => $project])
        ->call('selectTable', $table->id)
        ->assertSet('selectedTableId', $table->id)
        ->call('deleteTable', $table->id)
        ->assertSet('selectedTableId', null)
        ->assertDispatched('table-selected', tableId: null);
});

it('dispatches schema-updated when a table is created', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(TablePanel::class, ['project' => $project])
        ->set('form.name', 'users')
        ->call('createTable')
        ->assertDispatched('schema-updated');
});
