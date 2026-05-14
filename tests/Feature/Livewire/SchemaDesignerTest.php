<?php

use App\Livewire\SchemaDesigner;
use App\Models\Project;
use App\Models\User;

it('requires authentication', function () {
    $project = Project::factory()->create();

    $this->get(route('projects.show', $project))
        ->assertRedirect(route('login'));
});

it('renders for the project owner', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('projects.show', $project))
        ->assertSuccessful()
        ->assertSeeLivewire(SchemaDesigner::class);
});

it('returns 403 for a non-owner', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($other)
        ->get(route('projects.show', $project))
        ->assertForbidden();
});

it('displays the project name in the header', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'E-Commerce Schema',
    ]);

    $this->actingAs($user)
        ->get(route('projects.show', $project))
        ->assertSee('E-Commerce Schema');
});

it('renders child components', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('projects.show', $project))
        ->assertSeeLivewire('table-panel')
        ->assertSeeLivewire('column-editor')
        ->assertSeeLivewire('pivot-manager');
});
