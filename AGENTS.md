# Interactive Schema Visualizer (ISV)

**Stack**: PHP 8.5 · Laravel 13 · Livewire 4 · MariaDB · Vite 8 · Tailwind CSS 4 · Mermaid.js · Laravel Breeze

**Hierarchy**: User → Project → ProjectTable → TableColumn → PivotRelationship (M2M)

## Essentials
- Action classes for business logic, Livewire Forms for validation
- Pest tests, run `vendor/bin/pint --format agent` after PHP changes
- Every change must have tests
- PHP 8.5: constructor promotion, backed enums, readonly DTOs, return types
- Named routes via `route()` helper

## Commands
| Task | Command |
|---|---|
| Tests | `php artisan test --compact` |
| Format | `vendor/bin/pint --format agent` |
| Fresh DB | `php artisan migrate:fresh --seed` |
| Routes | `php artisan route:list --except-vendor` |

## Knowledge files (loaded automatically)
- `.opencode/knowledge/architecture.md` — Domain model, event flow, sync pipelines
- `.opencode/knowledge/patterns.md` — Coding conventions, architectural decisions
- `.opencode/knowledge/commands.md` — Full command reference + MCP tool docs

App served by Laravel Herd at `http://test-models.test`. Do not run `php artisan serve`.

## Session Memory
At the end of each session, save key context to the knowledge graph via memory tools (`memory_create_entities`, `memory_add_observations`). This persists information across sessions.

### Important Takeaways — Persistence Criteria
Save items matching **any** of the following categories:

| Category | What to save |
|---|---|
| **Major Decisions** | Architectural choices, package selections, design rationales |
| **User Preferences** | UI themes, communication style, workflow habits, tooling preferences |
| **Milestones** | Completed features, successful migrations, passing test suites |
| **Active Context** | Current focus area, active file paths, known blockers, in-progress work |
| **Project Facts** | Environment details, version constraints, service endpoints, config changes |
| **Code Patterns** | Project-specific naming conventions, logic patterns, conventions established this session |
| **User Requests** | Feature requests, bug reports, questions asked, feedback given |

### Persistence Rules
- **Be concise** — save the essence, not the transcript. A few sentences per entity is enough.
- **Prefer structured entities** — use distinct entity names for distinct subjects (e.g., `session-YYYY-MM-DD`, `project-X`, `deploy-Y`).
- **Overwrite stale observations** — use `memory_add_observations` to append new facts; don't duplicate old ones.
- **Include timestamps** — when saving session-scoped info, note the date (e.g., "As of 2026-05-16: ...").
- **When in doubt, save it** — it's better to persist something irrelevant than to lose something useful.
