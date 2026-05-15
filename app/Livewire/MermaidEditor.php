<?php

namespace App\Livewire;

use App\Actions\Mermaid\GenerateMermaidAction;
use App\Models\Project;
use App\Services\SchemaSyncService;
use Exception;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class MermaidEditor extends Component
{
    public Project $project;
    public string $dsl = '';
    public ?string $errorMessage = null;
    public bool $isDirty = false;

    public function mount(Project $project): void
    {
        $this->project = $project;
        $this->refreshDsl();
    }

    #[On('schema-updated')]
    public function onSchemaUpdated(int $projectId): void
    {
        if ($this->project->id === $projectId && !$this->isDirty) {
            $this->refreshDsl();
        }
    }

    public function updatedDsl(): void
    {
        $this->isDirty = true;
        $this->errorMessage = null;
    }

    public function apply(SchemaSyncService $syncService): void
    {
        try {
            $syncService->diffAndApply($this->project, $this->dsl);
            $this->errorMessage = null;
            $this->isDirty = false;
            
            // Dispatch both to let UI know it was specifically the editor that caused it
            // and to trigger general schema update hooks.
            $this->dispatch('mermaid-applied', projectId: $this->project->id);
            $this->dispatch('schema-updated', projectId: $this->project->id);
        } catch (Exception $e) {
            $this->errorMessage = "Failed to parse or apply DSL: " . $e->getMessage();
        }
    }

    private function refreshDsl(): void
    {
        $action = app(GenerateMermaidAction::class);
        $this->dsl = $action->execute($this->project);
        $this->isDirty = false;
    }

    public function render(): View
    {
        return view('livewire.mermaid-editor');
    }
}
