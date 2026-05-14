<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectTable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectTable>
 */
class ProjectTableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->word(),
        ];
    }
}
