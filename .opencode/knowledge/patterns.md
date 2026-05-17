# ISV Coding Patterns

## Conventions
- Action classes for business logic (not controllers)
- Livewire Form Objects for validation
- Class-based Livewire components (not Volt/SFC)
- Pest for testing (not PHPUnit directly)
- Named routes with `route()` helper
- Curly braces for control structures, even single-line bodies
- Explicit return type declarations on all methods
- PHPDoc blocks over inline comments

## PHP 8.5 Features
- Constructor promotion
- Backed enums (ColumnType, IndexType)
- Readonly DTOs (ColumnDefinition, TableDefinition, PivotDefinition, SchemaDiff)
- Return types on everything

## Mermaid
- Client-side rendering via Mermaid.js (npm package)
- Dark theme: `theme: 'dark'`, `er: { useMaxWidth: true }`
- ER subset only (entities, relationships)

## Schema Sync Rules
- Schema changes use **new migrations** (never rewrite existing)
- ParseMermaidAction → TableDefinition[] + PivotDefinition[] → SchemaSyncService → SchemaDiff → apply
- Each Project is a single logical schema namespace (no multi-DB)

## Auth
- Laravel Breeze (Blade-based auth scaffolding)
- ProjectPolicy gates per-user access
- `auth` middleware on all project routes
