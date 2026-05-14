<?php

use App\Actions\Schema\UpdateTableAction;
use App\Models\ProjectTable;

it('renames a table', function () {
    $table = ProjectTable::factory()->create(['name' => 'old_name']);
    $action = new UpdateTableAction;

    $result = $action->execute($table, ['name' => 'new_name']);

    expect($result->name)->toBe('new_name');

    $this->assertDatabaseHas('project_tables', [
        'id' => $table->id,
        'name' => 'new_name',
    ]);
});
