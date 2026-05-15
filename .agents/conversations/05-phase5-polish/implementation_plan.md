# Phase 5: Polish & DX 💎

The goal of this phase is to bring the Interactive Schema Visualizer (ISV) to a production-quality state, focusing on user experience, developer experience, and polishing the final product.

## User Review Required

> [!IMPORTANT]
> Please review the approach for "Undo Support" and the "Migration Export".
> For undo support, I propose storing the state history as Mermaid DSL strings in the PHP Session to avoid database clutter.
> For migrations, I will generate a single consolidated migration file containing all the schema changes for the project. Is this acceptable?

## Proposed Changes

---

### UI & Core Dependencies

- Install `@alpinejs/sortable` via npm to enable drag-and-drop column reordering.
- Register the sortable plugin in `resources/js/app.js`.

#### [MODIFY] resources/js/app.js
#### [MODIFY] package.json

---

### Column Drag-and-Drop (5.1)

- Update the `ColumnEditor` Livewire component to use `@alpinejs/sortable` directives (`x-sort`).
- Add a `reorder` method to `ColumnEditor` that receives the new order and updates the `position` column of the `TableColumn` models via a new `ReorderColumnsAction`.

#### [MODIFY] resources/views/livewire/column-editor.blade.php
#### [MODIFY] app/Livewire/ColumnEditor.php
#### [NEW] app/Actions/Schema/ReorderColumnsAction.php

---

### Undo Support (5.2)

- Introduce an event listener in `SchemaDesigner` that listens to `schema-updated`.
- Before a new change is applied, save the current schema state (as a Mermaid DSL string) into the user's session cache (e.g., `session()->push("project.{$project->id}.history", $currentDsl)`).
- Add an `undo` method to `SchemaDesigner` that pops the last DSL string from the session and applies it back to the database using `SchemaSyncService->diffAndApply()`.
- Add an "Undo" button to the UI header that is only visible when the history stack is not empty.

#### [MODIFY] app/Livewire/SchemaDesigner.php
#### [MODIFY] resources/views/livewire/schema-designer.blade.php

---

### Export Functionalities (5.3 & 5.4)

- **Mermaid Export**: Add a method to download the Mermaid DSL as a `.mmd` file directly from the `MermaidPreview` component.
- **Migration Export**: Create an `ExportMigrationAction` that queries the project's tables, columns, and pivot relationships and builds a consolidated Laravel migration script (e.g., using `Schema::create` blocks). Add a download button in the `SchemaDesigner` header.

#### [NEW] app/Actions/Schema/ExportMigrationAction.php
#### [MODIFY] app/Livewire/MermaidPreview.php
#### [MODIFY] resources/views/livewire/mermaid-preview.blade.php
#### [MODIFY] resources/views/livewire/schema-designer.blade.php

---

### UI Polish & Responsiveness (5.5 & 5.7)

- Add `wire:loading` directives to buttons for visual feedback during network requests.
- Add `wire:loading.class="opacity-50"` on the main content areas during data fetching.
- Add empty states for when no tables or columns exist in the project.
- Modify the 3-panel layout in `schema-designer.blade.php` to stack vertically on mobile screens (`flex-col lg:flex-row`).

#### [MODIFY] resources/views/livewire/schema-designer.blade.php
#### [MODIFY] resources/views/livewire/table-panel.blade.php
#### [MODIFY] resources/views/livewire/column-editor.blade.php
#### [MODIFY] resources/views/livewire/pivot-manager.blade.php

---

### Demo Seeder (5.6)

- Update `DatabaseSeeder` to create a realistic "Demo Project" (e.g., an E-commerce schema with `users`, `products`, `orders`, and a `order_product` pivot) to allow for immediate testing after `migrate:fresh --seed`.

#### [MODIFY] database/seeders/DatabaseSeeder.php

## Verification Plan

### Automated Tests
- Write a Feature test for `ReorderColumnsAction` to ensure `position` updates correctly.
- Write a Feature test for `ExportMigrationAction` to ensure the generated PHP code is valid.
- Ensure the existing test suite continues to pass with `php artisan test`.

### Manual Verification
- Verify column drag-and-drop visually in the browser.
- Perform a series of actions (add table, add column) and click "Undo" to verify the database and UI revert correctly.
- Download the `.mmd` and `.php` migration files and verify their contents.
- Run `php artisan migrate:fresh --seed` and log in to view the pre-populated schema.
