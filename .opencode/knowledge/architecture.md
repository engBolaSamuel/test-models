# ISV Architecture

## Domain Model
User → Project → ProjectTable → TableColumn → PivotRelationship (M2M)

## Directory Structure
```
app/Actions/Schema/           — Create/Update/Delete/Reorder for tables, columns, pivots
app/Actions/Mermaid/          — GenerateMermaidAction, ParseMermaidAction
app/Contracts/                — MermaidGeneratorInterface, MermaidParserInterface
app/DTOs/                     — ColumnDefinition, TableDefinition, PivotDefinition, SchemaDiff
app/Enums/                    — ColumnType (string, integer, bigInteger, text, boolean...), IndexType (none, primary, unique, index)
app/Livewire/                 — Dashboard, SchemaDesigner, TablePanel, ColumnEditor, PivotManager, MermaidPreview, MermaidEditor
app/Livewire/Forms/           — TableForm, ColumnForm, PivotForm
app/Models/                   — User, Project, ProjectTable, TableColumn, PivotRelationship
app/Policies/                 — ProjectPolicy
app/Services/                 — SchemaSyncService (bidirectional sync orchestrator)
```

## Livewire Components

| Component | Role |
|---|---|
| `Dashboard` | Project listing + create modal |
| `SchemaDesigner` | Main workspace orchestrator (3-panel layout) |
| `TablePanel` | Left panel: table list + CRUD |
| `ColumnEditor` | Center panel: column editor for selected table |
| `PivotManager` | M2M relationship manager |
| `MermaidPreview` | Right panel: live ER diagram (Mermaid.js) |
| `MermaidEditor` | Raw DSL code editor (reverse sync) |

## Event Flow

| Event | Dispatched By | Listened By | Payload |
|---|---|---|---|
| `schema-updated` | Any Action | MermaidPreview, TablePanel, ColumnEditor, PivotManager | `projectId` |
| `table-selected` | TablePanel | ColumnEditor | `tableId` |
| `mermaid-applied` | MermaidEditor | All panels | `projectId` |

## Bidirectional Sync

**Forward** (UI → DB → Mermaid): User action → Livewire → Action class → DB persist → `schema-updated` event → GenerateMermaidAction → Mermaid.js re-renders

**Reverse** (Mermaid DSL → DB → UI): User edits DSL → ParseMermaidAction → SchemaSyncService.diffAndApply() → `schema-updated` event → all panels re-render

## Key Tables

`table_columns` enhanced: name, type, length, is_nullable, default_value, is_unsigned, index_type, position, fk_table, fk_column

`pivot_relationships`: project_id, table_one_id, table_two_id, pivot_table_name, with_timestamps
