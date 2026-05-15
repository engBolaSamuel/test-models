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
    public string $rightPanelMode = 'preview'; // 'preview' or 'editor'
    public int $historyCount = 0;

    public function mount(Project $project): void
    {
        Gate::authorize('view', $project);

        $this->project = $project;
        
        if (!session()->has($this->getHistoryKey())) {
            $this->snapshot();
        } else {
            $this->historyCount = count(session()->get($this->getHistoryKey(), [])) - 1;
        }
    }

    #[On('schema-updated')]
    public function onSchemaUpdated(int $projectId): void
    {
        if ($projectId === $this->project->id) {
            $this->snapshot();
        }
    }

    private function snapshot(): void
    {
        $action = new \App\Actions\Mermaid\GenerateMermaidAction;
        $dsl = $action->execute($this->project);
        
        $history = session()->get($this->getHistoryKey(), []);
        
        if (empty($history) || end($history) !== $dsl) {
            $history[] = $dsl;
            if (count($history) > 50) {
                array_shift($history);
            }
            session()->put($this->getHistoryKey(), $history);
            $this->historyCount = count($history) - 1;
        }
    }

    public function undo(): void
    {
        $history = session()->get($this->getHistoryKey(), []);
        
        if (count($history) > 1) {
            array_pop($history); // current state
            $previousDsl = end($history);
            
            $parser = new \App\Actions\Mermaid\ParseMermaidAction;
            $parsed = $parser->execute($previousDsl);
            
            $syncService = app(\App\Services\SchemaSyncService::class);
            $syncService->diffAndApply($this->project, $parsed['tables'], $parsed['pivots']);
            
            session()->put($this->getHistoryKey(), $history);
            $this->historyCount = count($history) - 1;
            
            $this->dispatch('schema-updated', projectId: $this->project->id);
            $this->dispatch('mermaid-applied', projectId: $this->project->id);
        }
    }

    public function exportMigration()
    {
        $action = new \App\Actions\Schema\ExportMigrationAction;
        $content = $action->execute($this->project);
        
        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, date('Y_m_d_His') . '_create_' . \Illuminate\Support\Str::slug($this->project->name, '_') . '_schema.php');
    }

    public function exportMermaid()
    {
        $action = new \App\Actions\Mermaid\GenerateMermaidAction;
        $content = $action->execute($this->project);
        
        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, \Illuminate\Support\Str::slug($this->project->name, '_') . '-schema.mmd');
    }

    private function getHistoryKey(): string
    {
        return "project_{$this->project->id}_history";
    }

    public function render(): View
    {
        return view('livewire.schema-designer');
    }
}
