<?php

use App\Actions\Schema\DeletePivotRelationshipAction;
use App\Models\PivotRelationship;

it('deletes a pivot relationship', function () {
    $pivot = PivotRelationship::factory()->create();
    $action = new DeletePivotRelationshipAction;

    $action->execute($pivot);

    $this->assertDatabaseMissing('pivot_relationships', ['id' => $pivot->id]);
});
