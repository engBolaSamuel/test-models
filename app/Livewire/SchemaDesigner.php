<?php

namespace App\Livewire;

use App\Actions\Mermaid\GenerateMermaidAction;
use App\Actions\Mermaid\ParseMermaidAction;
use App\Actions\Schema\ExportMigrationAction;
use App\Models\Project;
use App\Services\SchemaSyncService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
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

        if (! session()->has($this->getHistoryKey())) {
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
        $action = new GenerateMermaidAction;
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

            $parser = new ParseMermaidAction;
            $parsed = $parser->execute($previousDsl);

            $syncService = app(SchemaSyncService::class);
            $syncService->diffAndApply($this->project, $parsed['tables'], $parsed['pivots']);

            session()->put($this->getHistoryKey(), $history);
            $this->historyCount = count($history) - 1;

            $this->dispatch('schema-updated', projectId: $this->project->id);
            $this->dispatch('mermaid-applied', projectId: $this->project->id);
        }
    }

    public function exportMigration()
    {
        $action = new ExportMigrationAction;
        $content = $action->execute($this->project);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, date('Y_m_d_His').'_create_'.Str::slug($this->project->name, '_').'_schema.php');
    }

    public function exportMermaid()
    {
        $action = new GenerateMermaidAction;
        $content = $action->execute($this->project);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, Str::slug($this->project->name, '_').'-schema.mmd');
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
