<?php

namespace App\Actions\Schema;

use App\Models\PivotRelationship;

class DeletePivotRelationshipAction
{
    public function execute(PivotRelationship $pivot): void
    {
        $pivot->delete();
    }
}
