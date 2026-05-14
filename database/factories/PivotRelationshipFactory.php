<?php

namespace Database\Factories;

use App\Models\PivotRelationship;
use App\Models\Project;
use App\Models\ProjectTable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PivotRelationship>
 */
class PivotRelationshipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $project = Project::factory()->create();

        return [
            'project_id' => $project->id,
            'table_one_id' => ProjectTable::factory()->create(['project_id' => $project->id])->id,
            'table_two_id' => ProjectTable::factory()->create(['project_id' => $project->id])->id,
            'pivot_table_name' => fake()->word().'_'.fake()->word(),
            'with_timestamps' => true,
        ];
    }

    /**
     * Indicate that the pivot table should not have timestamps.
     */
    public function withoutTimestamps(): static
    {
        return $this->state(fn (array $attributes) => [
            'with_timestamps' => false,
        ]);
    }
}
