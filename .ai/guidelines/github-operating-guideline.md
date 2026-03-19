# Git Workflow and Change Control Guideline for AI Agent

## Branching Rule
- Before starting any task, always check out the latest `main` branch.
- Pull the newest changes from `main`.
- Create a new branch for the task.
- Never work directly on `main`.

### Branch Naming
Use a clear and descriptive branch name, such as:
- `feature/add-user-notifications`
- `fix/login-validation-error`
- `refactor/order-service-cleanup`

Suggested format:
- `feature/<short-description>`
- `fix/<short-description>`
- `chore/<short-description>`
- `refactor/<short-description>`

---

## Required Flow Before Making Changes
1. Switch to `main`.
2. Pull latest `main`.
3. Create a fresh task branch from `main`.
4. Confirm current branch is not `main`.
5. Start implementation only after branch creation.

Example flow:

```bash
git checkout main
git pull origin main
git checkout -b feature/<task-name>
```

---

## Development Rule
- Make changes only inside the newly created branch.
- Keep changes scoped to the requested task.
- Avoid unrelated edits.
- Preserve existing functionality unless the task explicitly requires a change.

---

## Testing Rule
After completing code changes:
- Run all relevant tests.
- Run linting/formatting checks if available.
- Fix failing tests before requesting review.
- Do not mark the task complete if tests are failing, unless explicitly reported to the user.

Examples:

```bash
php artisan test
composer test
npm test
npm run build
```

Use the commands relevant to the project.

---

## Commit Rule
- Do not commit automatically without permission.
- Prepare changes in small, logical, procedural steps.
- Share a summary of completed work with the admin/user.
- Ask for admin permission before creating commits.

### Commit Procedure
When permission is granted:
1. Review changed files.
2. Group related changes logically.
3. Create clear and focused commits.
4. Use meaningful commit messages.

Example commit message styles:
- `fix: correct login validation flow`
- `feat: add patient search filters`
- `refactor: simplify invoice calculation service`

---

## Completion Rule
When implementation is finished:
1. Provide a summary of what was changed.
2. Report test results clearly.
3. Ask the user/admin to test the changes.
4. Wait for user confirmation before committing and pushing.

---

## Push Rule
- Do not push code automatically after implementation.
- Do not push code automatically after commit.
- Push only after explicit user consent.

### Final Sequence
1. Implementation completed
2. Tests passed
3. User/admin reviews and tests
4. User gives consent
5. Commit changes
6. Push branch to remote

---

## Safety Rules
- Never commit directly to `main`.
- Never push directly to `main`.
- Never skip branch creation.
- Never skip tests unless the user explicitly approves.
- Never push without user consent.
- Never assume permission; wait for explicit approval.

---

## Expected AI Agent Behavior
For every task, the AI agent must:
- start from updated `main`
- create a new branch
- perform only task-related changes
- run relevant tests
- summarize results
- wait for admin permission before commit
- wait for user consent before push

---

## Example Operational Checklist
- [ ] Checkout `main`
- [ ] Pull latest changes
- [ ] Create new task branch
- [ ] Implement requested changes
- [ ] Run relevant tests
- [ ] Summarize changes for user/admin
- [ ] Request permission to commit
- [ ] Wait for user testing
- [ ] Request consent to push
- [ ] Push only after approval