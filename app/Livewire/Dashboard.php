<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard — My Projects')]
class Dashboard extends Component
{
    public string $name = '';

    public string $description = '';

    /** @var array<string, list<string>> */
    protected array $rules = [
        'name' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string', 'max:1000'],
    ];

    public function createProject(): void
    {
        $validated = $this->validate();

        Auth::user()->projects()->create($validated);

        $this->reset('name', 'description');
        $this->dispatch('close-modal', id: 'create-project');

        session()->flash('message', 'Project created successfully.');
    }

    public function deleteProject(Project $project): void
    {
        $this->authorize('delete', $project);

        $project->delete();

        session()->flash('message', 'Project deleted successfully.');
    }

    public function render(): View
    {
        return view('livewire.dashboard', [
            'projects' => Auth::user()
                ->projects()
                ->withCount('tables')
                ->latest()
                ->get(),
        ]);
    }
}
