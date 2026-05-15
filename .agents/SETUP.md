---
trigger: always
---

# Agent Cold-Start Setup Guide

> **Purpose**: This file is for YOU, the AI coding agent (Antigravity / Gemini).
> Read this on first interaction with this project on a new machine to restore full context.

---

## 1. What This Project Is

**Interactive Schema Visualizer (ISV)** — a "Notion-like" database design tool with bidirectional sync:
- **Forward**: UI → DB → Mermaid.js ER diagram
- **Reverse**: Edit Mermaid DSL → parse → sync back to DB → UI updates

**Stack**: PHP 8.5 · Laravel 13 · Livewire 4 · MariaDB · Vite 8 · Tailwind CSS 4 · Mermaid.js · Laravel Breeze

**Status**: All 5 phases COMPLETED ✅ (Foundation → Forward UI → Forward Mermaid → Reverse Sync → Polish)

---

## 2. Critical Files to Read First

Read these files IN ORDER before doing any work:

| Priority | File | What You'll Learn |
|---|---|---|
| 🔴 1 | `.agents/knowledge/ISV_PROJECT_PLAN.md` | Full architecture, domain model, sync pipelines, component map, all decisions |
| 🔴 2 | `.agents/rules/laravel-boost.md` | Coding conventions, MCP tool usage, test enforcement rules |
| 🔴 3 | `.agents/rules/frontend-standards.md` | Livewire 4 as primary framework, Vite asset bundling |
| 🟡 4 | `.gemini/knowledge/project_structure.md` | Current file tree (run update-tree skill if stale) |
| 🟡 5 | `GEMINI.md` (project root) | Laravel Boost guidelines, skill activation rules, PHP/Pint/Pest conventions |

---

## 3. Project Hierarchy

```
User → Project → ProjectTable → TableColumn
               → PivotRelationship (M2M between two ProjectTables)
```

Key models: `User`, `Project`, `ProjectTable`, `TableColumn`, `PivotRelationship`

---

## 4. Architecture Quick Reference

### Layers
- **Livewire Components** (`app/Livewire/`) — UI + state
- **Action Classes** (`app/Actions/Schema/`, `app/Actions/Mermaid/`) — business logic
- **DTOs** (`app/DTOs/`) — `TableDefinition`, `ColumnDefinition`, `PivotDefinition`, `SchemaDiff`
- **Enums** (`app/Enums/`) — `ColumnType`, `IndexType`
- **Services** (`app/Services/SchemaSyncService.php`) — bidirectional sync orchestrator
- **Policies** (`app/Policies/ProjectPolicy.php`) — authorization

### Livewire Component Map
| Component | Role |
|---|---|
| `Dashboard` | Project listing + create modal |
| `SchemaDesigner` | Main workspace orchestrator (3-panel layout) |
| `TablePanel` | Left panel: table list + CRUD |
| `ColumnEditor` | Center panel: column editor for selected table |
| `PivotManager` | M2M relationship manager |
| `MermaidPreview` | Right panel: live ER diagram (client-side Mermaid.js) |
| `MermaidEditor` | Raw DSL code editor (reverse sync) |

### Event Flow
| Event | Dispatched By | Listened By |
|---|---|---|
| `schema-updated` | Any Action | `MermaidPreview`, `TablePanel`, `ColumnEditor`, `PivotManager` |
| `table-selected` | `TablePanel` | `ColumnEditor` |
| `mermaid-applied` | `MermaidEditor` | All panels |

---

## 5. New Machine Setup Checklist

When the user clones this repo on a new machine, help them run these commands:

### 5.1 Prerequisites
- PHP 8.5 (via Laravel Herd)
- MariaDB/MySQL
- Node.js + npm
- Composer

### 5.2 Installation Commands

```bash
# 1. Install PHP dependencies
composer install

# 2. Install JS dependencies
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database (update .env with DB credentials first)
#    DB_CONNECTION=mysql
#    DB_DATABASE=test_models
#    DB_USERNAME=root
#    DB_PASSWORD=
php artisan migrate --seed

# 5. Build frontend assets
npm run build

# 6. Verify everything works
php artisan test --compact
```

### 5.3 Laravel Herd
The app is served by Herd at `http://test-models.test` (or `https://` if secured).
Do NOT run `php artisan serve`. Use the `get-absolute-url` tool to confirm the URL.

### 5.4 MCP Server
The Laravel Boost MCP is configured in `.gemini/settings.json`. It should work automatically after `composer install`.

---

## 6. Conversation Archive

All past development conversations are preserved in `.agents/conversations/`.
Each folder contains an `overview.txt` (full transcript) and any artifacts (screenshots, plans, recordings).

### Reading Order (chronological development history)

| Folder | Phase | Summary |
|---|---|---|
| `00a-fix-database-connection/` | Setup | Fixed MySQL `performance_schema` permission error |
| `00b-verify-route-connection/` | Setup | Verified Laravel Boost MCP connection |
| `00c-generate-project-structure/` | Setup | Created PROJECT_STRUCTURE.md via PowerShell script |
| `00d-update-project-structure/` | Setup | Synchronized structure docs |
| `01-architecture-planning/` | Phase 0 | Designed full ISV architecture + 5-phase roadmap |
| `02-isv-foundation/` | Phase 1 | Models, migrations, DTOs, enums, Breeze, factories |
| `03a-schema-designer-dashboard/` | Phase 2 | Dashboard, SchemaDesigner, TablePanel, ColumnEditor, PivotManager |
| `03b-forward-pipeline-mermaid-pivots/` | Phase 3 | GenerateMermaidAction, MermaidPreview, pivot entity rendering |
| `04-reverse-sync-phase/` | Phase 4 | ParseMermaidAction, SchemaSyncService, MermaidEditor |
| `05-phase5-polish/` | Phase 5 | Modal fixes, layout reorganization, final polish |
| `06-publish-to-github/` | Meta | This conversation — publishing agent data to repo |

### When to Read Conversations
- **Debugging a feature**: Read the conversation that built it for original intent and edge cases
- **Extending a component**: Read its creation conversation for design decisions
- **Understanding "why"**: The overviews capture rejected alternatives and rationale

---

## 7. Knowledge Items Archive

Global Antigravity knowledge items are archived in `.agents/antigravity-knowledge/`.
Each contains `metadata.json` (summary, references) and `artifacts/` (the actual content).

| Item | Contains |
|---|---|
| `isv-project-plan/` | Finalized architecture document (same as `.agents/knowledge/ISV_PROJECT_PLAN.md`) |
| `project-structure/` | File tree snapshot |

---

## 8. Skills Available

Activate these skills when working in their domain (read `SKILL.md` first):

| Skill | When to Use |
|---|---|
| `laravel-best-practices` | Any Laravel PHP code |
| `livewire-development` | Any Livewire component work |
| `pest-testing` | Writing or fixing tests |
| `tailwindcss-development` | Any Tailwind/UI styling |
| `update-tree` | Refreshing PROJECT_STRUCTURE.md |

---

## 9. Conventions to Follow

1. **Action classes** for business logic (not controllers)
2. **Livewire Form Objects** for validation
3. **Class-based Livewire** components (not Volt/SFC)
4. **Pest** for testing (not PHPUnit directly)
5. **Run `vendor/bin/pint --dirty --format agent`** after PHP changes
6. **Every change must have tests** — run `php artisan test --compact` with filter
7. **Use `php artisan make:*`** to create files
8. **Named routes** with `route()` helper
9. **PHP 8.5 features**: constructor promotion, backed enums, readonly DTOs, return types

---

## 10. Common Tasks Reference

| Task | Command |
|---|---|
| Run all tests | `php artisan test --compact` |
| Run specific test | `php artisan test --compact --filter=TestName` |
| Format PHP | `vendor/bin/pint --dirty --format agent` |
| Fresh DB with seed data | `php artisan migrate:fresh --seed` |
| Build frontend | `npm run build` |
| Dev server (frontend HMR) | `npm run dev` |
| List routes | `php artisan route:list --except-vendor` |
| Update project structure | Run the `update-tree` skill |
| Check app info | Use `application-info` MCP tool |
| Check DB schema | Use `database-schema` MCP tool |
| Search Laravel docs | Use `search-docs` MCP tool |
