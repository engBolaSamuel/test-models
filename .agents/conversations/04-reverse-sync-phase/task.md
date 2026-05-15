# Phase 4 Tasks: Reverse Pipeline

- `[x]` **1. ParseMermaidAction**
  - `[x]` Create `app/Actions/Mermaid/ParseMermaidAction.php`
  - `[x]` Implement robust regex-based parser for ER subset
  - `[x]` Implement pivot table detection
  - `[x]` Write `ParseMermaidActionTest.php`

- `[x]` **2. SchemaDiff DTO**
  - `[x]` Create `app/DTOs/SchemaDiff.php`
  - `[x]` Write unit test for diff structure

- `[x]` **3. SchemaSyncService**
  - `[x]` Create `app/Services/SchemaSyncService.php`
  - `[x]` Implement `diffAndApply()` method wrapping existing Actions
  - `[x]` Write `SchemaSyncServiceTest.php`

- `[x]` **4. MermaidEditor Component**
  - `[x]` Create `app/Livewire/MermaidEditor.php`
  - `[x]` Create `mermaid-editor.blade.php` view (textarea UI)
  - `[x]` Implement `apply()` method to invoke sync
  - `[x]` Write `MermaidEditorTest.php`

- `[x]` **5. UI Integration**
  - `[x]` Update `schema-designer.blade.php` to add toggle between Preview and Editor tabs
  - `[x]` Ensure events fire correctly across components
