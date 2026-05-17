<?php

namespace App\Livewire;

use App\Actions\Schema\CreateColumnAction;
use App\Actions\Schema\DeleteColumnAction;
use App\Actions\Schema\ReorderColumnsAction;
use App\Actions\Schema\UpdateColumnAction;
use App\Enums\ColumnType;
use App\Enums\IndexType;
use App\Livewire\Forms\ColumnForm;
use App\Models\Project;
use App\Models\ProjectTable;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ColumnEditor extends Component
{
    public Project $project;

    public ?int $selectedTableId = null;

    public ColumnForm $form;

    public ?int $editingColumnId = null;

    public bool $showAddForm = false;

    #[On('table-selected')]
    public function onTableSelected(?int $tableId): void
    {
        $this->selectedTableId = $tableId;
        $this->resetForm();
    }

    public function showAdd(): void
    {
        $this->editingColumnId = null;
        $this->form->reset();
        $this->showAddForm = true;
    }

    public function createColumn(): void
    {
        $this->form->validate();

        $table = $this->getSelectedTable();
        $action = new CreateColumnAction;
        $action->execute($table, $this->form->toArray());

        $this->resetForm();
        $this->dispatch('schema-updated', projectId: $this->project->id);
    }

    public function editColumn(int $columnId): void
    {
        $table = $this->getSelectedTable();
        $column = $table->columns()->findOrFail($columnId);

        $this->editingColumnId = $columnId;
        $this->showAddForm = false;
        $this->form->setFromColumn($column);
    }

    public function updateColumn(): void
    {
        $this->form->validate();

        $table = $this->getSelectedTable();
        $column = $table->columns()->findOrFail($this->editingColumnId);

        $action = new UpdateColumnAction;
        $action->execute($column, $this->form->toArray());

        $this->resetForm();
        $this->dispatch('schema-updated', projectId: $this->project->id);
    }

    public function deleteColumn(int $columnId): void
    {
        $table = $this->getSelectedTable();
        $column = $table->columns()->findOrFail($columnId);

        $action = new DeleteColumnAction;
        $action->execute($column);

        if ($this->editingColumnId === $columnId) {
            $this->resetForm();
        }

        $this->dispatch('schema-updated', projectId: $this->project->id);
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    public function reorder(array $orderedIds): void
    {
        $table = $this->getSelectedTable();
        $action = new ReorderColumnsAction;
        $action->execute($table, $orderedIds);

        $this->dispatch('schema-updated', projectId: $this->project->id);
    }

    #[On('schema-updated')]
    public function refreshColumns(): void
    {
        // Re-render triggers fresh query
    }

    public function render(): View
    {
        $columns = [];
        $selectedTable = null;

        if ($this->selectedTableId) {
            $selectedTable = $this->project->tables()->find($this->selectedTableId);
            if ($selectedTable) {
                $columns = $selectedTable->columns()->orderBy('position')->get();
            }
        }

        return view('livewire.column-editor', [
            'columns' => $columns,
            'selectedTable' => $selectedTable,
            'columnTypes' => ColumnType::cases(),
            'indexTypes' => IndexType::cases(),
        ]);
    }

    private function getSelectedTable(): ProjectTable
    {
        return $this->project->tables()->findOrFail($this->selectedTableId);
    }

    private function resetForm(): void
    {
        $this->editingColumnId = null;
        $this->showAddForm = false;
        $this->form->reset();
    }
}
