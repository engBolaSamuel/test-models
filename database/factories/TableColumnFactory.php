<?php

namespace Database\Factories;

use App\Enums\ColumnType;
use App\Enums\IndexType;
use App\Models\ProjectTable;
use App\Models\TableColumn;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TableColumn>
 */
class TableColumnFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_table_id' => ProjectTable::factory(),
            'name' => fake()->word(),
            'type' => ColumnType::String,
            'is_primary' => false,
            'is_nullable' => false,
            'default_value' => null,
            'is_unsigned' => false,
            'length' => null,
            'position' => 0,
            'index_type' => null,
            'fk_table' => null,
            'fk_column' => null,
        ];
    }

    /**
     * Indicate that the column is a primary key.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'id',
            'type' => ColumnType::UnsignedBigInteger,
            'is_primary' => true,
            'is_unsigned' => true,
            'index_type' => IndexType::Primary,
        ]);
    }

    /**
     * Indicate that the column is nullable.
     */
    public function nullable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_nullable' => true,
        ]);
    }

    /**
     * Indicate that the column is a foreign key.
     */
    public function foreignKey(string $table, string $column = 'id'): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ColumnType::UnsignedBigInteger,
            'is_unsigned' => true,
            'index_type' => IndexType::Index,
            'fk_table' => $table,
            'fk_column' => $column,
        ]);
    }
}
