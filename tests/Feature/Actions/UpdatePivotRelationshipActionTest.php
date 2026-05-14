<?php

use App\Actions\Schema\UpdatePivotRelationshipAction;
use App\Models\PivotRelationship;

it('updates a pivot relationship', function () {
    $pivot = PivotRelationship::factory()->create(['with_timestamps' => true]);
    $action = new UpdatePivotRelationshipAction;

    $result = $action->execute($pivot, [
        'pivot_table_name' => 'custom_pivot',
        'with_timestamps' => false,
    ]);

    expect($result)
        ->pivot_table_name->toBe('custom_pivot')
        ->with_timestamps->toBeFalse();
});
