<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Schema Designer')]
class SchemaDesigner extends Component
{
    public Project $project;

    public function mount(Project $project): void
    {
        Gate::authorize('view', $project);

        $this->project = $project;
    }

    public function render(): View
    {
        return view('livewire.schema-designer');
    }
}
