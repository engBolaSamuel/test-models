<?php

namespace App\Livewire;

use App\Actions\Mermaid\GenerateMermaidAction;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class MermaidPreview extends Component
{
    public Project $project;

    public string $mermaidDsl = '';

    // TODO: Uncomment for live auto-update in next ISV release
    // #[On('schema-updated')]
    public function generateDiagram(): void
    {
        $action = new GenerateMermaidAction;
        $this->mermaidDsl = $action->execute($this->project);
    }

    public function render(): View
    {
        return view('livewire.mermaid-preview');
    }
}
