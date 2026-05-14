<?php

namespace App\Livewire;

use App\Actions\Schema\CreatePivotRelationshipAction;
use App\Actions\Schema\DeletePivotRelationshipAction;
use App\Actions\Schema\UpdatePivotRelationshipAction;
use App\Livewire\Forms\PivotForm;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class PivotManager extends Component
{
    public Project $project;

    public PivotForm $form;

    public ?int $editingPivotId = null;

    public bool $showAddForm = false;

    public function showAdd(): void
    {
        $this->editingPivotId = null;
        $this->form->reset();
        $this->showAddForm = true;
    }

    public function createPivot(): void
    {
        $this->form->validate();

        $action = new CreatePivotRelationshipAction;
        $action->execute($this->project, $this->form->toArray());

        $this->resetForm();
        $this->dispatch('schema-updated', projectId: $this->project->id);
    }

    public function editPivot(int $pivotId): void
    {
        $pivot = $this->project->pivotRelationships()->findOrFail($pivotId);

        $this->editingPivotId = $pivotId;
        $this->showAddForm = false;
        $this->form->setFromPivot($pivot);
    }

    public function updatePivot(): void
    {
        $this->form->validate();

        $pivot = $this->project->pivotRelationships()->findOrFail($this->editingPivotId);

        $action = new UpdatePivotRelationshipAction;
        $action->execute($pivot, $this->form->toArray());

        $this->resetForm();
        $this->dispatch('schema-updated', projectId: $this->project->id);
    }

    public function deletePivot(int $pivotId): void
    {
        $pivot = $this->project->pivotRelationships()->findOrFail($pivotId);

        $action = new DeletePivotRelationshipAction;
        $action->execute($pivot);

        if ($this->editingPivotId === $pivotId) {
            $this->resetForm();
        }

        $this->dispatch('schema-updated', projectId: $this->project->id);
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    #[On('schema-updated')]
    public function refreshPivots(): void
    {
        // Re-render triggers fresh query
    }

    public function render(): View
    {
        return view('livewire.pivot-manager', [
            'pivots' => $this->project->pivotRelationships()
                ->with(['tableOne', 'tableTwo'])
                ->get(),
            'tables' => $this->project->tables()->orderBy('name')->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->editingPivotId = null;
        $this->showAddForm = false;
        $this->form->reset();
    }
}
