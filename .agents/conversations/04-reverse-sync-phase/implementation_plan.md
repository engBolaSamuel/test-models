# Phase 4: Reverse Pipeline — Mermaid to Database

This implementation plan covers Phase 4 of the ISV project, enabling bidirectional synchronization by allowing the user to edit the raw Mermaid.js DSL text and applying those changes back to the Eloquent models and database structure.

## Proposed Changes

### 1. `app/Actions/Mermaid/ParseMermaidAction.php`
[NEW] `ParseMermaidAction.php`
- A robust parser for a subset of the Mermaid ER diagram DSL.
- Processes line-by-line using regex to extract:
  - **Tables**: `TableName { ... }` blocks.
  - **Columns**: Inside table blocks, format: `type name modifiers`. Maps Mermaid types (`varchar`, `bigint`, etc.) back to the `ColumnType` enum. Identifies `PK` and `FK` modifiers.
  - **Relationships**: Lines matching `TableA ||--o{ TableB : "label"`. 
- Distinguishes between standard Tables and Pivot Tables:
  - If a table is the target of exactly two `||--o{` relationships (e.g., `User ||--o{ UserRole` and `Role ||--o{ UserRole`), it is recognized as a Pivot Table.
  - Returns a tuple/array containing `TableDefinition[]` and `PivotDefinition[]`.

### 2. `app/DTOs/SchemaDiff.php`
[NEW] `SchemaDiff.php`
- A Data Transfer Object representing the delta between the current database state (Eloquent models) and the parsed Mermaid schema.
- Properties:
  - `tablesToCreate`, `tablesToUpdate` (rename), `tablesToDelete`
  - `columnsToCreate`, `columnsToUpdate`, `columnsToDelete`
  - `pivotsToCreate`, `pivotsToDelete`
- Helper logic or a dedicated diffing engine to compare the arrays of parsed definitions against the `ProjectTable`, `TableColumn`, and `PivotRelationship` Eloquent collections.

### 3. `app/Services/SchemaSyncService.php`
[NEW] `SchemaSyncService.php`
- Orchestrates the reverse sync pipeline.
- Method `diffAndApply(Project $project, string $mermaidDsl): SchemaDiff`:
  - Parses the DSL via `ParseMermaidAction`.
  - Computes the difference to populate a `SchemaDiff` object.
  - Wraps the application in a `DB::transaction()`.
  - Loops over the diff properties and delegates to the existing CRUD Actions (`CreateTableAction`, `DeleteTableAction`, `CreateColumnAction`, etc.) to persist changes.

### 4. `app/Livewire/MermaidEditor.php`
[NEW] `MermaidEditor.php` (Component & View)
- A Livewire component for the right-side panel providing the raw DSL code editor.
- Integrates a textarea (enhanced with Alpine.js / CodeMirror for syntax highlighting).
- Action method `apply()`:
  - Validates the input string.
  - Calls `SchemaSyncService->diffAndApply()`.
  - Dispatches the `schema-updated` event to trigger re-renders on the Left/Center panels and the `MermaidPreview` diagram.
- Exception handling: catches parse/diff errors and displays an inline `$errorMessage`.

### 5. `resources/views/livewire/schema-designer.blade.php`
[MODIFY] `schema-designer.blade.php`
- Update the layout to include a toggle or tab to switch between `MermaidPreview` and `MermaidEditor` components, or display them depending on the UI mode. (Requires user confirmation on how they want the toggling handled).

---

## User Review Required

> [!IMPORTANT]
> **Pivot Table Detection**: The Mermaid DSL doesn't explicitly mark a block as a "PivotRelationship". I will determine it by checking if a table acts as the "many" side for exactly two other tables. Does this logic align with your expectations? 

> [!IMPORTANT]
> **Update Table/Column Logic**: During diffing, standard practice is to use the `name` as the identifier. If a user renames a table or column in the Mermaid DSL, the diffing engine will likely see it as a "Delete old name" and "Create new name". Do you want to support explicit renaming in Mermaid, or is delete/recreate acceptable for Mermaid-driven renames?

## Open Questions

> [!WARNING]
> How should the `MermaidEditor` be integrated visually into the 3-panel layout? Should it replace the `MermaidPreview` panel via a toggle button (e.g., "Visual Diagram" vs "Code Editor" tabs), or be placed somewhere else?

## Verification Plan

### Automated Tests
- **Unit/Feature Tests**: 
  - `ParseMermaidActionTest` using a known DSL snippet and verifying the exact structure of returned Definitions.
  - `SchemaSyncServiceTest` starting with a blank database, syncing a DSL string, and asserting the DB structure matches.
  - Testing the rename/delete flow by syncing a modified DSL string and asserting tables/columns are correctly updated/dropped.

### Manual Verification
- Render the `SchemaDesigner` in the browser.
- Edit the Mermaid text, add a table and column, hit "Apply", and verify the left/center panels auto-update.
- Introduce a syntax error in the DSL and ensure the UI handles the exception gracefully without crashing.
