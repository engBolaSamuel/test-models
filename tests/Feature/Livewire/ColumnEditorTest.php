<?php

use App\Enums\ColumnType;
use App\Livewire\ColumnEditor;
use App\Models\Project;
use App\Models\ProjectTable;
use App\Models\TableColumn;
use App\Models\User;
use Livewire\Livewire;

it('shows no-table-selected state initially', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(ColumnEditor::class, ['project' => $project])
        ->assertSee('Select a table from the left panel');
});

it('shows columns when table is selected via event', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $table = ProjectTable::factory()->create(['project_id' => $project->id]);
    TableColumn::factory()->create([
        'project_table_id' => $table->id,
        'name' => 'email',
        'type' => ColumnType::String,
    ]);

    Livewire::actingAs($user)
        ->test(ColumnEditor::class, ['project' => $project])
        ->dispatch('table-selected', tableId: $table->id)
        ->assertSee('email')
        ->assertSee('String');
});

it('can create a new column', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $table = ProjectTable::factory()->create(['project_id' => $project->id]);

    Livewire::actingAs($user)
        ->test(ColumnEditor::class, ['project' => $project])
        ->dispatch('table-selected', tableId: $table->id)
        ->call('showAdd')
        ->set('form.name', 'username')
        ->set('form.type', 'string')
        ->call('createColumn')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('table_columns', [
        'project_table_id' => $table->id,
        'name' => 'username',
        'type' => 'string',
    ]);
});

it('validates column name is required', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $table = ProjectTable::factory()->create(['project_id' => $project->id]);

    Livewire::actingAs($user)
        ->test(ColumnEditor::class, ['project' => $project])
        ->dispatch('table-selected', tableId: $table->id)
        ->call('showAdd')
        ->set('form.name', '')
        ->call('createColumn')
        ->assertHasErrors(['form.name' => 'required']);
});

it('can edit an existing column', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $table = ProjectTable::factory()->create(['project_id' => $project->id]);
    $column = TableColumn::factory()->create([
        'project_table_id' => $table->id,
        'name' => 'old_name',
        'type' => ColumnType::String,
    ]);

    Livewire::actingAs($user)
        ->test(ColumnEditor::class, ['project' => $project])
        ->dispatch('table-selected', tableId: $table->id)
        ->call('editColumn', $column->id)
        ->assertSet('form.name', 'old_name')
        ->set('form.name', 'new_name')
        ->set('form.type', 'text')
        ->call('updateColumn')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('table_columns', [
        'id' => $column->id,
        'name' => 'new_name',
        'type' => 'text',
    ]);
});

it('can delete a column', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $table = ProjectTable::factory()->create(['project_id' => $project->id]);
    $column = TableColumn::factory()->create(['project_table_id' => $table->id]);

    Livewire::actingAs($user)
        ->test(ColumnEditor::class, ['project' => $project])
        ->dispatch('table-selected', tableId: $table->id)
        ->call('deleteColumn', $column->id);

    $this->assertDatabaseMissing('table_columns', ['id' => $column->id]);
});

it('dispatches schema-updated when a column is created', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $table = ProjectTable::factory()->create(['project_id' => $project->id]);

    Livewire::actingAs($user)
        ->test(ColumnEditor::class, ['project' => $project])
        ->dispatch('table-selected', tableId: $table->id)
        ->call('showAdd')
        ->set('form.name', 'email')
        ->set('form.type', 'string')
        ->call('createColumn')
        ->assertDispatched('schema-updated');
});

it('resets form when switching tables', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $table1 = ProjectTable::factory()->create(['project_id' => $project->id]);
    $table2 = ProjectTable::factory()->create(['project_id' => $project->id]);

    Livewire::actingAs($user)
        ->test(ColumnEditor::class, ['project' => $project])
        ->dispatch('table-selected', tableId: $table1->id)
        ->call('showAdd')
        ->set('form.name', 'partial_input')
        ->dispatch('table-selected', tableId: $table2->id)
        ->assertSet('showAddForm', false)
        ->assertSet('form.name', '');
});
