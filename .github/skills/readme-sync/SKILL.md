---
name: readme-sync
description: "Ensures README.md stays in sync after any project change. Activate after completing ANY task that modifies features, tech stack, Docker setup, environment variables, scripts, AI skills/guidelines, branching rules, installation steps, or project structure. Also activate when the user explicitly mentions README, documentation sync, or updating the readme. Covers: adding/removing features, new/changed npm or composer scripts, Docker image or service changes, new skills or guidelines, route changes, new panels or modules, dependency version bumps, and workflow/PR rule updates. Do NOT activate for pure code-only changes (bug fixes, refactors, test additions) that don't affect any README-documented topic."
---

# README Sync

Keep `README.md` accurate after every project change that affects user-facing documentation.

## When to Activate

This skill triggers after completing any task that changes something documented in the README. Check if the completed work touches ANY of these areas:

| README Section | Trigger Changes |
|----------------|----------------|
| **Features** | New feature, removed feature, changed workflow, new panel, new module |
| **Tech Stack** | Dependency version bump, new package added, package removed |
| **Interactions & Tutorials** | New workflow, changed panel URLs, new keyboard shortcut, UI flow change |
| **Installation** | Docker service added/removed, new env variable required, new prerequisite |
| **Development Setup** | New script in `composer.json` or `package.json`, changed dev workflow, new branching rule, PR checklist update |
| **Publishing to Docker** | Dockerfile changes, new build arg, image tag convention change, new service |
| **AI Setup** | New skill added, skill removed, new guideline file, boost.json change, AGENTS.md structure change |

## Sync Procedure

After completing the primary task, follow these steps:

### Step 1 — Identify Affected Sections

Read the current `README.md` and identify which sections are impacted by the changes just made. Compare against the trigger table above.

### Step 2 — Read Current State

Read the specific section(s) of `README.md` that need updating. Do NOT rewrite the entire file — make targeted edits only.

### Step 3 — Apply Minimal Updates

Update only the affected section(s). Follow these rules:

- **Add** new items to existing lists/tables when a feature, script, service, or skill is added.
- **Remove** items that no longer exist.
- **Update** values that changed (version numbers, URLs, command names).
- **Preserve** the existing format, tone, and structure — do not restyle or reorganize.
- **Do not** add commentary or changelogs — the README should read as current truth, not a history.

### Step 4 — Verify Badge Accuracy

If the change involved a major dependency version bump, check the badges at the top of the file:

```html
<img src="https://img.shields.io/badge/PHP-8.4-..." />
<img src="https://img.shields.io/badge/Laravel-12-..." />
<img src="https://img.shields.io/badge/Filament-4-..." />
<img src="https://img.shields.io/badge/React-19-..." />
<img src="https://img.shields.io/badge/Inertia.js-2-..." />
<img src="https://img.shields.io/badge/Tailwind_CSS-4-..." />
```

Update the version number in badges only if the major version changed.

## Section Reference

Current README structure (sections and what they document):

### Features
- Patient Management (registration, search, history)
- Counter & Financial Operations (closings, transactions, vouchers, receivables, PDF reports)
- Service Orders & Clinical Workflows (departments, queues, treatment lifecycle)
- Admin Panel — Filament v4 (resources, reports, widgets, RBAC)
- Accounts Panel
- Compliance & Security (PHC, HIPAA, audit trail, soft deletes)
- Progressive URL Resolution (hierarchical URL pattern)

### Tech Stack
Table mapping layers to technologies. Update when packages are added/removed or versions bumped.

### Interactions & Tutorials
- Panel Navigation table (Patient Register, Counter, Queues, Admin, Accounts)
- Workflow walkthroughs (Patient Registration, Service Order Treatment, Counter Closing, Expense Voucher)
- Keyboard shortcut (kbar command palette)

### Installation
- Docker quick start steps
- Access Points table (ports)
- Docker Services table
- Published Docker image pull commands

### Development Setup
- Local prerequisites
- `composer run dev` service table (server, queue, logs, vite)
- Available Scripts tables (PHP/Backend, JavaScript/Frontend, Testing, Code Quality)
- Branching & PR Rules (branch naming, required flow, PR checklist)

### Publishing to Docker
- Image tag convention (`{version}`, `{version}-cli`)
- Build commands with Sentry build args
- Push commands
- Production `docker-compose.yml` example
- Sentry integration table

### AI Setup (Laravel Boost & Skills)
- Boost tools table
- `.ai/guidelines/` file listing table
- Skills table with activation triggers
- Setup steps (install, enable MCP, verify skills, verify guidelines)
- AGENTS.md description

## Common Sync Scenarios

### New Feature Added
1. Add bullet point(s) under the appropriate Features subsection.
2. If the feature introduces a new workflow, add a workflow walkthrough under Interactions & Tutorials.
3. If it adds a new panel or URL pattern, update the Panel Navigation table.

### New npm/composer Script Added
1. Add the script to the appropriate "Available Scripts" table in Development Setup.

### New Docker Service Added
1. Add to Docker Services table in Installation.
2. Add port to Access Points table.
3. Update `docker-compose.yml` production example in Publishing to Docker if relevant.

### New AI Skill Created
1. Add row to Skills table in AI Setup section.

### New `.ai/guidelines/` File Created
1. Add row to guidelines file listing table in AI Setup section.

### New Environment Variable Required
1. If it's required for setup, mention in Installation steps.
2. If it's a build arg, update the Sentry/build args table in Publishing to Docker.

### Package Version Bumped
1. Update the Tech Stack table.
2. Update the version badge if major version changed.

### Branching or PR Rules Changed
1. Update the Branching & PR Rules section in Development Setup.
2. Update the PR Checklist if items changed.

## Anti-Patterns

- **Do not** rewrite sections that weren't affected by the change.
- **Do not** add "last updated" timestamps or changelog entries.
- **Do not** duplicate information already in `docs/project-description.md` — the README is a concise overview, not the full spec.
- **Do not** add new sections to the README without explicit user request.
- **Do not** remove the Table of Contents or badges section.
