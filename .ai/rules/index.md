# Project Rules Index

Before planning or editing, find the row whose globs match the file's path and read that rule file. Backend globs are relative to `be/`; frontend globs are relative to the repo root (`fe/...`).

| Applies to                                | Rule file                         |
| ----------------------------------------- | --------------------------------- |
| `database/migrations/**`                  | `.ai/rules/migrations.md`         |
| `fe/src/pages/**`                         | `.ai/rules/pages.md`              |
| `fe/src/pages/Admin/PersonaManagement/**` | `.ai/rules/persona-management.md` |
| `fe/src/services/**`                      | `.ai/rules/frontend-cache.md`     |
| `fe/src/**/__tests__/**`                  | `.ai/rules/frontend-cache.md`     |
