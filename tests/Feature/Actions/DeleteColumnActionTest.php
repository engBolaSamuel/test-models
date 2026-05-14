<?php

use App\Actions\Schema\DeleteColumnAction;
use App\Models\TableColumn;

it('deletes a column', function () {
    $column = TableColumn::factory()->create();
    $action = new DeleteColumnAction;

    $action->execute($column);

    $this->assertDatabaseMissing('table_columns', ['id' => $column->id]);
});
