<?php

use App\Actions\Schema\UpdateColumnAction;
use App\Enums\ColumnType;
use App\Models\TableColumn;

it('updates column attributes', function () {
    $column = TableColumn::factory()->create([
        'name' => 'old_name',
        'type' => ColumnType::String,
    ]);
    $action = new UpdateColumnAction;

    $result = $action->execute($column, [
        'name' => 'new_name',
        'type' => ColumnType::Text->value,
        'is_nullable' => true,
    ]);

    expect($result)
        ->name->toBe('new_name')
        ->type->toBe(ColumnType::Text)
        ->is_nullable->toBeTrue();
});
