<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'table_one_id',
    'table_two_id',
    'pivot_table_name',
    'with_timestamps',
])]
class PivotRelationship extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'with_timestamps' => 'boolean',
        ];
    }

    /** @return BelongsTo<Project, $this> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** @return BelongsTo<ProjectTable, $this> */
    public function tableOne(): BelongsTo
    {
        return $this->belongsTo(ProjectTable::class, 'table_one_id');
    }

    /** @return BelongsTo<ProjectTable, $this> */
    public function tableTwo(): BelongsTo
    {
        return $this->belongsTo(ProjectTable::class, 'table_two_id');
    }
}
