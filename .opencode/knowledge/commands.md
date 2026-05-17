# ISV Commands

| Task | Command |
|---|---|
| Run all tests | `php artisan test --compact` |
| Run specific test | `php artisan test --compact --filter=TestName` |
| Format PHP | `vendor/bin/pint --format agent` |
| Fresh DB with seed | `php artisan migrate:fresh --seed` |
| Build frontend | `npm run build` |
| Dev server (HMR) | `npm run dev` |
| Dev all services | `composer run dev` |
| List routes | `php artisan route:list --except-vendor` |
| Make Livewire component | `php artisan make:livewire ComponentName` |

## Laravel Boost MCP Tools
- `database-query` — Run read-only DB queries
- `database-schema` — Inspect table structure
- `search-docs` — Search version-specific Laravel docs
- `get-absolute-url` — Resolve project URLs
- `browser-logs` — Read browser errors/exceptions
- `application-info` — Check app info

## Herd
App served at `http://test-models.test`. Do not run `php artisan serve`.
