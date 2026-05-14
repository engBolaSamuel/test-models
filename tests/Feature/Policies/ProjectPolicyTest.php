<?php

use App\Models\Project;
use App\Models\User;

it('allows owner to view their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    expect($user->can('view', $project))->toBeTrue();
});

it('prevents non-owner from viewing a project', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $owner->id]);

    expect($other->can('view', $project))->toBeFalse();
});

it('allows owner to update their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    expect($user->can('update', $project))->toBeTrue();
});

it('prevents non-owner from updating a project', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $owner->id]);

    expect($other->can('update', $project))->toBeFalse();
});

it('allows owner to delete their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    expect($user->can('delete', $project))->toBeTrue();
});

it('prevents non-owner from deleting a project', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $owner->id]);

    expect($other->can('delete', $project))->toBeFalse();
});

it('allows any user to create a project', function () {
    $user = User::factory()->create();

    expect($user->can('create', Project::class))->toBeTrue();
});
