# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

> Starter rules for a project scaffolded from the BaseAPI template
> (PHP 8.4+, `timanthonyalexander/base-api`, the `mason` CLI). Extend per project,
> but the conventions below are framework-level and apply everywhere.

## Table Naming (CRITICAL)

Database table names are **singular** `snake_case` of the model class name, with NO
pluralization: `Brand` → `brand`, `JobTask` → `job_task`, `ApiToken` → `api_token`.
When writing raw SQL / `mysql` queries, use the singular form (pluralizing returns
"table doesn't exist"). Override only via the model's `public static ?string $table`.

## Migrations (CRITICAL — never violate)

The migration generator is the single source of truth for schema.
**Correct models ⇒ correct migrations, always.** The ONLY way to change schema:

1. Edit the **model** PHP class (add/change a property, `$columns`, `$indexes`).
2. `php mason migrate:generate` — records the delta in `storage/migrations.json`.
3. `php mason migrate:apply -y` — applies it locally.

Deploys auto-apply whatever is in `storage/migrations.json`. That file is how schema
reaches staging/prod.

**Hard rules — no exceptions, ever:**

- **NEVER run manual `ALTER TABLE` / `CREATE TABLE` / `DROP` / any raw DDL** to change
  schema. Manual DDL bypasses the generator, so the change never lands in
  `migrations.json`, so it never reaches prod (which only applies what's in that file)
  — silently breaking every model write that touches the new column.
- **NEVER hand-edit `storage/migrations.json`.** Only `migrate:generate` writes it.
- **NEVER use `--safe`. ALWAYS run ALL migrations, including "destructive" ones.**
  There is no "skip destructive migrations" rule and never was.
- If `migrate:generate` proposes a destructive or wrong operation, **a model is wrong
  — fix the model**, then regenerate. The fix is NEVER to skip migrations, cherry-pick
  which to run, or hand-ALTER. Correct models ⇒ correct migrations.
- To undo a manual/incorrect schema state: revert the DB to match the models, then
  `migrate:generate` (it finds the real delta) → `migrate:apply -y`.

> Real failure this prevents: a `channel` column was hand-`ALTER`ed onto a table
> locally (to dodge the generator's "destructive" drift), so the migration never
> entered `migrations.json`. The model deployed to prod but the column did not — every
> INSERT threw "Unknown column" and orders were silently dropped for hours. Running the
> generator and fixing models (not `--safe`, not manual ALTER) prevents all of it.

## Models

- BaseAPI Active Record. Public typed properties map to columns; declare `$columns` /
  `$indexes` for types and indexes. No automatic timestamps — manage `created_at` /
  `updated_at` manually unless the model opts in.
- After any model/schema change, run the migration workflow above.
