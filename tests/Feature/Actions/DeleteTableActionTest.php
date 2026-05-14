<?php

use App\Actions\Schema\DeleteTableAction;
use App\Models\ProjectTable;
use App\Models\TableColumn;

it('deletes a table', function () {
    $table = ProjectTable::factory()->create();
    $action = new DeleteTableAction;

    $action->execute($table);

    $this->assertDatabaseMissing('project_tables', ['id' => $table->id]);
});

it('cascades deletion to columns', function () {
    $table = ProjectTable::factory()->create();
    $column = TableColumn::factory()->create(['project_table_id' => $table->id]);
    $action = new DeleteTableAction;

    $action->execute($table);

    $this->assertDatabaseMissing('table_columns', ['id' => $column->id]);
});
