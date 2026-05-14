<?php

namespace App\Models;

use App\Enums\ColumnType;
use App\Enums\IndexType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_table_id',
    'name',
    'type',
    'is_primary',
    'is_nullable',
    'default_value',
    'is_unsigned',
    'length',
    'position',
    'index_type',
    'fk_table',
    'fk_column',
])]
class TableColumn extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ColumnType::class,
            'index_type' => IndexType::class,
            'is_primary' => 'boolean',
            'is_nullable' => 'boolean',
            'is_unsigned' => 'boolean',
            'length' => 'integer',
            'position' => 'integer',
        ];
    }

    /** @return BelongsTo<ProjectTable, $this> */
    public function projectTable(): BelongsTo
    {
        return $this->belongsTo(ProjectTable::class);
    }
}
