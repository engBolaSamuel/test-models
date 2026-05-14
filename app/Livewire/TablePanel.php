<?php

namespace App\Livewire;

use App\Actions\Schema\CreateTableAction;
use App\Actions\Schema\DeleteTableAction;
use App\Actions\Schema\UpdateTableAction;
use App\Livewire\Forms\TableForm;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class TablePanel extends Component
{
    public Project $project;

    public TableForm $form;

    public ?int $selectedTableId = null;

    public ?int $editingTableId = null;

    public string $editingName = '';

    public function selectTable(int $tableId): void
    {
        $this->selectedTableId = $tableId;
        $this->dispatch('table-selected', tableId: $tableId);
    }

    public function createTable(): void
    {
        $this->form->validate();

        $action = new CreateTableAction;
        $table = $action->execute($this->project, $this->form->toArray());

        $this->form->reset();
        $this->selectTable($table->id);
        $this->dispatch('schema-updated', projectId: $this->project->id);
    }

    public function startEditing(int $tableId): void
    {
        $table = $this->project->tables()->findOrFail($tableId);
        $this->editingTableId = $tableId;
        $this->editingName = $table->name;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editingName' => ['required', 'string', 'max:255'],
        ]);

        $table = $this->project->tables()->findOrFail($this->editingTableId);
        $action = new UpdateTableAction;
        $action->execute($table, ['name' => $this->editingName]);

        $this->cancelEdit();
        $this->dispatch('schema-updated', projectId: $this->project->id);
    }

    public function cancelEdit(): void
    {
        $this->editingTableId = null;
        $this->editingName = '';
    }

    public function deleteTable(int $tableId): void
    {
        $table = $this->project->tables()->findOrFail($tableId);
        $action = new DeleteTableAction;
        $action->execute($table);

        if ($this->selectedTableId === $tableId) {
            $this->selectedTableId = null;
            $this->dispatch('table-selected', tableId: null);
        }

        $this->dispatch('schema-updated', projectId: $this->project->id);
    }

    #[On('schema-updated')]
    public function refreshTables(): void
    {
        // Re-render triggers fresh query
    }

    public function render(): View
    {
        return view('livewire.table-panel', [
            'tables' => $this->project->tables()->withCount('columns')->orderBy('name')->get(),
        ]);
    }
}
