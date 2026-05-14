<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'description'])]
class Project extends Model
{
    use HasFactory;

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<ProjectTable, $this> */
    public function tables(): HasMany
    {
        return $this->hasMany(ProjectTable::class);
    }

    /** @return HasMany<PivotRelationship, $this> */
    public function pivotRelationships(): HasMany
    {
        return $this->hasMany(PivotRelationship::class);
    }
}
