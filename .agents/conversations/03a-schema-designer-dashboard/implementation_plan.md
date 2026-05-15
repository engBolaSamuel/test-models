# Phase 2 (Steps 2.2–2.10): Forward Pipeline — UI to Database

Complete the Livewire CRUD UI for tables, columns, and pivot relationships with authorization.

## Proposed Changes

### Component 1: Schema Designer Layout (Step 2.2)

Full-page 3-panel layout at `/projects/{project}`.

#### [NEW] `app/Livewire/SchemaDesigner.php`
- Class-based Livewire component, receives `Project` via route model binding
- Passes `$project` to child components
- Uses `#[Layout('layouts.app')]`

#### [NEW] `resources/views/livewire/schema-designer.blade.php`
- 3-panel responsive grid: Left (TablePanel) | Center (ColumnEditor) | Right (PivotManager)
- Renders `<livewire:table-panel>`, `<livewire:column-editor>`, `<livewire:pivot-manager>` as child components

#### [MODIFY] `routes/web.php`
- Add `Route::livewire('/projects/{project}', SchemaDesigner::class)` inside auth group

#### [MODIFY] `resources/views/livewire/dashboard.blade.php`
- Make project cards clickable → link to `projects.show` route

---

### Component 2: Table Actions + Form (Step 2.3)

#### [NEW] `app/Actions/Schema/CreateTableAction.php`
- Validates & creates a `ProjectTable` on a given `Project`
- Returns the created `ProjectTable`

#### [NEW] `app/Actions/Schema/UpdateTableAction.php`
- Validates & renames an existing `ProjectTable`

#### [NEW] `app/Actions/Schema/DeleteTableAction.php`
- Deletes a `ProjectTable` and its columns (cascade from DB)

#### [NEW] `app/Livewire/Forms/TableForm.php`
- Livewire Form Object with `name` field + validation rules

#### [NEW] `tests/Feature/Actions/CreateTableActionTest.php`
#### [NEW] `tests/Feature/Actions/UpdateTableActionTest.php`
#### [NEW] `tests/Feature/Actions/DeleteTableActionTest.php`

---

### Component 3: TablePanel Component (Step 2.4)

#### [NEW] `app/Livewire/TablePanel.php`
- Lists all tables for a project
- Add/rename/delete tables using Actions
- Dispatches `table-selected` event on click

#### [NEW] `resources/views/livewire/table-panel.blade.php`
- Table list with add button, inline rename, delete button

#### [NEW] `tests/Feature/Livewire/TablePanelTest.php`

---

### Component 4: Column Actions + Form (Step 2.5)

#### [NEW] `app/Actions/Schema/CreateColumnAction.php`
#### [NEW] `app/Actions/Schema/UpdateColumnAction.php`
#### [NEW] `app/Actions/Schema/DeleteColumnAction.php`
#### [NEW] `app/Actions/Schema/ReorderColumnsAction.php`

#### [NEW] `app/Livewire/Forms/ColumnForm.php`
- All column fields: name, type, is_nullable, default_value, is_unsigned, length, index_type, fk_table, fk_column

#### [NEW] `tests/Feature/Actions/CreateColumnActionTest.php`
#### [NEW] `tests/Feature/Actions/UpdateColumnActionTest.php`
#### [NEW] `tests/Feature/Actions/DeleteColumnActionTest.php`

---

### Component 5: ColumnEditor Component (Step 2.6)

#### [NEW] `app/Livewire/ColumnEditor.php`
- Listens for `table-selected` event
- Lists columns for selected table
- Full CRUD using column Actions

#### [NEW] `resources/views/livewire/column-editor.blade.php`
- Column list/form with ColumnType and IndexType dropdowns

#### [NEW] `tests/Feature/Livewire/ColumnEditorTest.php`

---

### Component 6: Pivot Actions + Form (Step 2.7)

#### [NEW] `app/Actions/Schema/CreatePivotRelationshipAction.php`
#### [NEW] `app/Actions/Schema/UpdatePivotRelationshipAction.php`
#### [NEW] `app/Actions/Schema/DeletePivotRelationshipAction.php`

#### [NEW] `app/Livewire/Forms/PivotForm.php`
- Fields: table_one_id, table_two_id, pivot_table_name, with_timestamps

#### [NEW] `tests/Feature/Actions/CreatePivotRelationshipActionTest.php`
#### [NEW] `tests/Feature/Actions/UpdatePivotRelationshipActionTest.php`
#### [NEW] `tests/Feature/Actions/DeletePivotRelationshipActionTest.php`

---

### Component 7: PivotManager Component (Step 2.8)

#### [NEW] `app/Livewire/PivotManager.php`
- Lists pivot relationships for project
- CRUD using pivot Actions

#### [NEW] `resources/views/livewire/pivot-manager.blade.php`
- Relationship list with two table dropdowns

#### [NEW] `tests/Feature/Livewire/PivotManagerTest.php`

---

### Component 8: Event Wiring (Step 2.9)

Handled within the components above:
- `TablePanel` dispatches `table-selected` with `tableId`
- `ColumnEditor` listens via `#[On('table-selected')]`

---

### Component 9: ProjectPolicy + Route Authorization (Step 2.10)

#### [NEW] `app/Policies/ProjectPolicy.php`
- `view`: user owns project
- `update`: user owns project
- `delete`: user owns project

#### [MODIFY] `app/Livewire/Dashboard.php`
- Use policy for delete authorization instead of inline check

#### [MODIFY] `app/Livewire/SchemaDesigner.php`
- Authorize via `Gate::authorize('view', $project)` in mount

#### [NEW] `tests/Feature/Policies/ProjectPolicyTest.php`

---

## Verification Plan

### Automated Tests
```bash
php artisan test --compact
```
All action tests, component tests, and policy tests must pass.

### Manual Verification
- Browser: navigate dashboard → click project → see 3-panel layout
- Add/rename/delete tables in left panel
- Select table → columns appear in center panel
- Add/edit/delete columns
- Create pivot relationships in right panel
- Verify User A cannot access User B's project URL
