# README Sync Conventions

Keep `README.md` accurate after every project change. The README is the first thing users and contributors see — it must reflect the current state of the project.

---

## Rule

**After completing any task**, check if the changes affect something documented in `README.md`. If they do, update the relevant section(s) before finishing.

---

## Tracked Sections

| README Section | Update When |
|----------------|------------|
| **Features** | New feature added, feature removed, workflow changed, new panel or module |
| **Tech Stack** | Package added/removed, major version bumped |
| **Interactions & Tutorials** | Panel URL changed, new workflow, new keyboard shortcut |
| **Installation** | Docker service added/removed, new env variable required, new prerequisite |
| **Development Setup** | Script added/changed in `composer.json` or `package.json`, branching rule changed, PR checklist updated |
| **Publishing to Docker** | Dockerfile changed, new build arg, image tag convention changed |
| **AI Setup** | Skill added/removed, guideline file added/removed, `boost.json` changed |

---

## How to Update

- **Read** the affected section(s) of `README.md` before editing.
- **Make targeted edits only** — do not rewrite unaffected sections.
- **Add** new items to existing lists/tables when something is introduced.
- **Remove** items that no longer exist.
- **Update** values that changed (versions, URLs, command names).
- **Update badges** at the top if a major dependency version changed.

---

## Examples

### New Feature
Add bullet point(s) under the matching Features subsection.

### New Script (`composer.json` or `package.json`)
Add row to the relevant "Available Scripts" table in Development Setup.

### New Docker Service
Add to Docker Services table + Access Points table in Installation.

### New AI Skill
Add row to Skills table in AI Setup section.

### New Guideline File (`.ai/guidelines/`)
Add row to guidelines file listing table in AI Setup section.

### Package Version Bump
Update Tech Stack table. Update badge if major version changed.

---

## Do Not

- Rewrite sections unrelated to the change.
- Add changelogs, "last updated" timestamps, or commentary.
- Duplicate detailed content from `docs/project-description.md` — the README is a concise overview.
- Add new top-level sections without explicit user request.
