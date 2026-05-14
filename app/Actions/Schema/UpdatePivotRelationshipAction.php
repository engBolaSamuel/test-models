<?php

namespace App\Actions\Schema;

use App\Models\PivotRelationship;

class UpdatePivotRelationshipAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(PivotRelationship $pivot, array $data): PivotRelationship
    {
        $pivot->update($data);

        return $pivot;
    }
}
