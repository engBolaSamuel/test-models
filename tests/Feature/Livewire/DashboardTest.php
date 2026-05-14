<?php

use App\Livewire\Dashboard;
use App\Models\Project;
use App\Models\User;
use Livewire\Livewire;

it('requires authentication', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

it('renders the dashboard component', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSeeLivewire(Dashboard::class);
});

it('displays the empty state when user has no projects', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(Dashboard::class)
        ->assertSee('No projects yet')
        ->assertSee('Get started by creating your first database schema project.');
});

it('lists only the authenticated user projects', function () {
    $user = User::factory()->create();
    $ownProject = Project::factory()->create(['user_id' => $user->id, 'name' => 'My Schema']);
    $otherProject = Project::factory()->create(['name' => 'Other Schema']);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('My Schema')
        ->assertDontSee('Other Schema');
});

it('displays project table counts', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $project->tables()->createMany([
        ['name' => 'users'],
        ['name' => 'posts'],
    ]);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('2 tables');
});

it('can create a new project', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('name', 'Blog Platform')
        ->set('description', 'A blog schema design')
        ->call('createProject')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('projects', [
        'user_id' => $user->id,
        'name' => 'Blog Platform',
        'description' => 'A blog schema design',
    ]);
});

it('resets form fields after creating a project', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('name', 'Blog Platform')
        ->set('description', 'A blog schema design')
        ->call('createProject')
        ->assertSet('name', '')
        ->assertSet('description', '')
        ->assertDispatched('close-modal', id: 'create-project');
});

it('validates that name is required when creating a project', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('name', '')
        ->call('createProject')
        ->assertHasErrors(['name' => 'required']);

    $this->assertDatabaseCount('projects', 0);
});

it('validates that name cannot exceed 255 characters', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('name', str_repeat('a', 256))
        ->call('createProject')
        ->assertHasErrors(['name' => 'max']);
});

it('can delete an owned project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('deleteProject', $project->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});

it('cannot delete another user project', function () {
    $user = User::factory()->create();
    $otherProject = Project::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('deleteProject', $otherProject->id)
        ->assertForbidden();
});

it('shows projects ordered by latest first', function () {
    $user = User::factory()->create();

    $older = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Older Project',
        'created_at' => now()->subDay(),
    ]);

    $newer = Project::factory()->create([
        'user_id' => $user->id,
        'name' => 'Newer Project',
        'created_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSeeInOrder(['Newer Project', 'Older Project']);
});

it('allows creating a project with no description', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('name', 'Minimal Project')
        ->call('createProject')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('projects', [
        'user_id' => $user->id,
        'name' => 'Minimal Project',
        'description' => '',
    ]);
});
