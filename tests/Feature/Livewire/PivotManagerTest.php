<?php

use App\Livewire\PivotManager;
use App\Models\PivotRelationship;
use App\Models\Project;
use App\Models\ProjectTable;
use App\Models\User;
use Livewire\Livewire;

it('displays empty state when no pivots exist', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(PivotManager::class, ['project' => $project])
        ->assertSee('No many-to-many relationships yet.');
});

it('displays existing pivot relationships', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $tableOne = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'users']);
    $tableTwo = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'roles']);
    PivotRelationship::factory()->create([
        'project_id' => $project->id,
        'table_one_id' => $tableOne->id,
        'table_two_id' => $tableTwo->id,
        'pivot_table_name' => 'role_user',
    ]);

    Livewire::actingAs($user)
        ->test(PivotManager::class, ['project' => $project])
        ->assertSee('users')
        ->assertSee('roles')
        ->assertSee('role_user');
});

it('can create a pivot relationship', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $tableOne = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'users']);
    $tableTwo = ProjectTable::factory()->create(['project_id' => $project->id, 'name' => 'roles']);

    Livewire::actingAs($user)
        ->test(PivotManager::class, ['project' => $project])
        ->call('showAdd')
        ->set('form.table_one_id', $tableOne->id)
        ->set('form.table_two_id', $tableTwo->id)
        ->set('form.pivot_table_name', 'role_user')
        ->call('createPivot')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('pivot_relationships', [
        'project_id' => $project->id,
        'table_one_id' => $tableOne->id,
        'table_two_id' => $tableTwo->id,
        'pivot_table_name' => 'role_user',
    ]);
});

it('validates that both tables are required', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(PivotManager::class, ['project' => $project])
        ->call('showAdd')
        ->set('form.table_one_id', null)
        ->set('form.table_two_id', null)
        ->call('createPivot')
        ->assertHasErrors(['form.table_one_id', 'form.table_two_id']);
});

it('validates that tables must be different', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $table = ProjectTable::factory()->create(['project_id' => $project->id]);

    Livewire::actingAs($user)
        ->test(PivotManager::class, ['project' => $project])
        ->call('showAdd')
        ->set('form.table_one_id', $table->id)
        ->set('form.table_two_id', $table->id)
        ->call('createPivot')
        ->assertHasErrors(['form.table_two_id']);
});

it('can delete a pivot relationship', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $tableOne = ProjectTable::factory()->create(['project_id' => $project->id]);
    $tableTwo = ProjectTable::factory()->create(['project_id' => $project->id]);
    $pivot = PivotRelationship::factory()->create([
        'project_id' => $project->id,
        'table_one_id' => $tableOne->id,
        'table_two_id' => $tableTwo->id,
    ]);

    Livewire::actingAs($user)
        ->test(PivotManager::class, ['project' => $project])
        ->call('deletePivot', $pivot->id);

    $this->assertDatabaseMissing('pivot_relationships', ['id' => $pivot->id]);
});

it('can edit a pivot relationship', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $tableOne = ProjectTable::factory()->create(['project_id' => $project->id]);
    $tableTwo = ProjectTable::factory()->create(['project_id' => $project->id]);
    $pivot = PivotRelationship::factory()->create([
        'project_id' => $project->id,
        'table_one_id' => $tableOne->id,
        'table_two_id' => $tableTwo->id,
        'pivot_table_name' => 'old_name',
        'with_timestamps' => true,
    ]);

    Livewire::actingAs($user)
        ->test(PivotManager::class, ['project' => $project])
        ->call('editPivot', $pivot->id)
        ->assertSet('form.pivot_table_name', 'old_name')
        ->set('form.pivot_table_name', 'new_name')
        ->set('form.with_timestamps', false)
        ->call('updatePivot')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('pivot_relationships', [
        'id' => $pivot->id,
        'pivot_table_name' => 'new_name',
        'with_timestamps' => false,
    ]);
});

it('dispatches schema-updated when a pivot is created', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $tableOne = ProjectTable::factory()->create(['project_id' => $project->id]);
    $tableTwo = ProjectTable::factory()->create(['project_id' => $project->id]);

    Livewire::actingAs($user)
        ->test(PivotManager::class, ['project' => $project])
        ->call('showAdd')
        ->set('form.table_one_id', $tableOne->id)
        ->set('form.table_two_id', $tableTwo->id)
        ->set('form.pivot_table_name', 'test_pivot')
        ->call('createPivot')
        ->assertDispatched('schema-updated');
});
