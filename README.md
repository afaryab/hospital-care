<p align="center">
  <img src="public/logo.png" alt="Hospital Care Logo" width="120" />
</p>

<h1 align="center">Hospital All In One Operations Software</h1>

<p align="center">
  A self-hosted, dockerized hospital management system covering patient registration, financial transactions, clinical treatments, inventory, asset management, and payroll — compliant with Punjab Healthcare Commission (PHC) guidelines and HIPAA-inspired practices.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white" alt="PHP 8.4" />
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel 12" />
  <img src="https://img.shields.io/badge/Filament-4-FBBF24?logo=laravel&logoColor=white" alt="Filament v4" />
  <img src="https://img.shields.io/badge/React-19-61DAFB?logo=react&logoColor=white" alt="React 19" />
  <img src="https://img.shields.io/badge/Inertia.js-2-9553E9?logo=inertia&logoColor=white" alt="Inertia.js v2" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS v4" />
  <img src="https://img.shields.io/badge/Docker-Ready-2496ED?logo=docker&logoColor=white" alt="Docker Ready" />
  <img src="https://github.com/afaryab/hospital-care/actions/workflows/pr-test.yaml/badge.svg" alt="PR Tests" />
  <img src="https://img.shields.io/badge/License-MIT-green" alt="MIT License" />
</p>

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Interactions & Tutorials](#interactions--tutorials)
- [Installation](#installation)
- [Development Setup](#development-setup)
- [Publishing to Docker](#publishing-to-docker)
- [AI Setup (Laravel Boost & Skills)](#ai-setup-laravel-boost--skills)
- [License](#license)

---

## Features

### Patient Management
- Patient registration with auto-numbered records (`PS/YYYY/MM/NNNN`)
- Patient search by name, CNIC, contact, or PS number
- Full patient history: transactions, service orders, receivables, treatments
- Human body diagram for recording affected areas

### Counter & Financial Operations
- Counter open/close lifecycle with closing statements (`CT/YYYY/MM/NNNN`)
- Transaction recording with auto-numbering (`TR/YYYY/MM/DD/NNNN`)
- Multi-department income tracking (OPD, Indoor, Emergency, Dental, Lab, Ultrasound, Radiology)
- Expense vouchers with approval workflow (`VC/YYYY/MM/NNNN`)
- Receivables tracking with partial payment support
- PDF reports: income, expense, receivable, and service summaries

### Service Orders & Clinical Workflows
- Department-specific service orders with configurable service catalogs
- Composite services that bundle multiple items
- Service provider assignment per department type
- Treatment queues for OPD, Indoor, Emergency, Dental, Lab, Ultrasound, Radiology

### Admin Panel (Filament v4)
- Full CRUD for Users, Patients, Closings, Transactions, Service Orders, Departments, Services
- Role-based access with multi-profile user system
- Report pages: Daily Report, Closing Report, Receivables Report, Settings
- Audit Log resource with user/model/date filtering for compliance review
- Dashboard widgets with key metrics

### Accounts Panel
- Dedicated accounting interface at `/accounts`
- Expense voucher management and financial reporting

### Compliance & Security
- Punjab Healthcare Commission (PHC) guideline compliance
- HIPAA-inspired audit trail and data protection
- Immutable record numbering with `lockForUpdate()` concurrency protection
- Soft deletes on all patient and financial records
- Role-based access control (RBAC) across panels

### Progressive URL Resolution
Every URL is hierarchical and independently resolvable:
```
/PS                              → All patients
/PS/2026                         → Patients from 2026
/PS/2026/03                      → Patients from March 2026
/PS/2026/03/0001                 → Individual patient
/COUNTER/CT/2026/03/0001         → Closing statement
/QUE/opd                         → OPD queue
```

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel 12, PHP 8.4 |
| **Admin Panel** | Filament v4, Livewire v3 |
| **Frontend** | React 19, Inertia.js v2, TypeScript |
| **Styling** | Tailwind CSS v4 |
| **Build Tool** | Vite v7 with React Compiler |
| **Database** | MySQL 8.0 |
| **Authentication** | Laravel Fortify (2FA, email verification) |
| **Route Generation** | Laravel Wayfinder (type-safe routes in TypeScript) |
| **PDF Generation** | DomPDF, mPDF |
| **Compliance Packages** | Spatie Activitylog, Spatie Permission, Spatie Medialibrary, Spatie Backup |
| **Error Tracking** | Sentry |
| **Monitoring** | Laravel Pulse, Telescope |
| **Testing** | Pest PHP v4 |
| **Code Quality** | Laravel Pint, ESLint, Prettier |
| **Containerization** | Docker Compose (Alpine Linux) |

---

## Interactions & Tutorials

### Panel Navigation

The application is divided into multiple panels, each with its own context and navigation:

| Panel | URL | Purpose |
|-------|-----|---------|
| **Patient Register** | `/PS/...` | Patient lookup, registration, history |
| **Counter** | `/COUNTER/...` | Open/close counters, record income/expenses |
| **Hospital Queues** | `/QUE/{department}` | Clinical queues per department (OPD, Indoor, Emergency, etc.) |
| **Admin** | `/admin` | Filament admin panel for system management |
| **Accounts** | `/accounts` | Accounting and financial operations |

### Typical Workflows

**1. Patient Registration & Transaction**
1. Open a counter → `/COUNTER`
2. Register a new patient → Patient gets a `PS/YYYY/MM/NNNN` number
3. Select department and service → Transaction is created
4. Transaction auto-numbered as `TR/YYYY/MM/DD/NNNN`
5. Close counter at end of shift → Closing statement generated

**2. Service Order Treatment**
1. Patient visits with a transaction → Service order created
2. Doctor opens department queue → `/QUE/opd`
3. Doctor treats patient, records diagnosis and prescription
4. Service order status transitions: `OPEN → IN_PROGRESS → TREATED → CLOSED`

**3. Counter Closing**
1. Receptionist navigates to counter panel
2. Reviews all transactions during the shift
3. Clicks "Close Counter" → Closing statement with breakdown
4. PDF reports available: income summary, expense summary, receivables

**4. Expense Voucher**
1. Create voucher from counter panel
2. Attach details: payee, amount, category
3. Submit for approval → Admin reviews in Filament panel
4. Approved vouchers appear in closing statement

### Key Keyboard Shortcuts

The application uses [kbar](https://github.com/timc1/kbar) for a command palette — press `Cmd+K` (Mac) or `Ctrl+K` (Windows/Linux) to search and navigate quickly.

---

## Installation

### Prerequisites

- Docker & Docker Compose
- Git

### Quick Start (Docker)

```bash
# Clone the repository
git clone https://github.com/afaryab/hospital-care.git
cd hospital-care

# Copy environment file
cp .env.example .env

# Configure database for Docker
# Edit .env and set:
#   DB_CONNECTION=mysql
#   DB_HOST=db
#   DB_PORT=3306
#   DB_DATABASE=hospital_care
#   DB_USERNAME=hc_user
#   DB_PASSWORD=hc_password

# Start all services
docker-compose up -d

# Run migrations inside the app container
docker-compose exec app php artisan migrate

# Seed initial data (optional)
docker-compose exec app php artisan db:seed

# Build frontend assets
docker-compose exec app pnpm run build
```

### Access Points

| Service | URL | Description |
|---------|-----|-------------|
| **Application** | http://localhost:8000 | Main application |
| **PhpMyAdmin** | http://localhost:8080 | Database management |
| **MySQL** | localhost:3306 | Direct database access |

### Docker Services

| Service | Image | Purpose |
|---------|-------|---------|
| `app` | `php:8.4-fpm-alpine` + Nginx | Web server (port 8000) |
| `cli` | `php:8.4-fpm-alpine` + SSH | Background jobs, CLI, schedule runner |
| `db` | `mysql:8.0` | Database (port 3306) |
| `pma` | `phpmyadmin/phpmyadmin` | DB admin interface (port 8080) |

### Using Published Docker Images

Pre-built images are published to Docker Hub:

```bash
# App container (web server)
docker pull ahmadfaryabkokab/hospital-care:{version}

# CLI container (jobs, schedules, SSH)
docker pull ahmadfaryabkokab/hospital-care:{version}-cli
```

---

## Development Setup

### Prerequisites

- PHP 8.4+
- Composer
- Node.js + pnpm
- MySQL 8.0
- Git

### Local Setup

```bash
# Clone and install dependencies
git clone https://github.com/afaryab/hospital-care.git
cd hospital-care
composer install
pnpm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed  # optional

# Start the development server (runs 4 services concurrently)
composer run dev
```

The `composer run dev` command starts all development services simultaneously:

| Service | Color | Purpose |
|---------|-------|---------|
| **server** | Blue | `php artisan serve` — Laravel dev server |
| **queue** | Purple | `php artisan queue:listen` — Job processor |
| **logs** | Pink | `php artisan pail` — Real-time log viewer |
| **vite** | Orange | `pnpm run dev` — Frontend HMR server |

### Available Scripts

**PHP / Backend:**
```bash
composer run dev          # Start all dev services
composer run dev:ssr      # Start with SSR mode
composer run test         # Run full test suite
composer run setup        # Fresh install (install, migrate, build)
```

**JavaScript / Frontend:**
```bash
pnpm run dev              # Vite dev server with HMR
pnpm run build            # Production build
pnpm run build:ssr        # Build with SSR support
pnpm run lint             # ESLint auto-fix
pnpm run format           # Prettier formatting
pnpm run format:check     # Check formatting without fixing
pnpm run types            # TypeScript type checking
```

**Testing:**
```bash
php artisan test --compact                           # All tests
php artisan test --compact --filter=testName          # Filter by name
php artisan test --compact tests/Feature/Auth/        # Specific directory
```

**Code Quality:**
```bash
vendor/bin/pint --dirty   # Format only modified PHP files
pnpm run build:ssr        # SSR build (must pass before PR)
pnpm run format:check     # Verify formatting (no auto-fix)
pnpm run lint             # ESLint check
pnpm run types            # TypeScript type check
```

### Branching & PR Rules

This project follows a strict branch-based workflow. **Never commit or push directly to `main`.**

**Branch Naming:**
```
feature/<short-description>    # New features
fix/<short-description>        # Bug fixes
chore/<short-description>      # Maintenance
refactor/<short-description>   # Code restructuring
```

**Required Flow:**
```bash
# Always start from an updated main
git checkout main
git pull origin main

# Create a task branch
git checkout -b feature/my-feature

# ... make changes ...

# Run tests before requesting review
php artisan test --compact
pnpm run build:ssr
pnpm run format:check
pnpm run lint
pnpm run types
vendor/bin/pint --dirty

# Push only after approval
git push origin feature/my-feature
```

**PR Checklist:**
- [ ] Branch created from latest `main`
- [ ] Changes are scoped to the requested task
- [ ] All tests pass (`php artisan test --compact`)
- [ ] SSR build passes (`pnpm run build:ssr`)
- [ ] Code formatting verified (`pnpm run format:check`)
- [ ] No lint errors (`pnpm run lint`)
- [ ] No TypeScript errors (`pnpm run types`)
- [ ] PHP code formatted (`vendor/bin/pint --dirty`)
- [ ] Summary of changes provided
- [ ] No direct commits to `main`

### CI Pipeline (Automated)

When a PR is opened against `main`, GitHub Actions builds the Docker app image and runs all checks inside it:

| Step | What it checks |
|------|---------------|
| **Docker Build** | Builds app image (PHP 8.4, Node.js, pnpm, all extensions) |
| **Pest Tests** | Runs test suite with coverage (min 30%) against MySQL |
| **Pint** | PHP code style validation |
| **TypeScript** | Type check (`pnpm run types`) |
| **ESLint** | Lint check (`pnpm run lint`) |
| **Prettier** | Format check (`pnpm run format:check`) |
| **SSR Build** | Vite SSR build (`pnpm run build:ssr`) |
| **CLI Image** | Verifies CLI Docker image builds successfully |

All checks run inside the Docker container to ensure the CI environment matches production. Both Docker images must build successfully before the PR can be merged.

---

## Publishing to Docker

The project publishes two Docker images per release to Docker Hub under `ahmadfaryabkokab/hospital-care`.

### Image Tags

| Tag | Container | Purpose |
|-----|-----------|---------|
| `{version}` | App | Nginx + PHP-FPM web server |
| `{version}-cli` | CLI | PHP-FPM + SSH for background jobs and CLI access |

### Building Images

```bash
# Build the app image
docker build \
  --build-arg SENTRY_RELEASE=v1.0.0 \
  --build-arg SENTRY_ENVIRONMENT=production \
  -f docker/app/Dockerfile \
  -t ahmadfaryabkokab/hospital-care:v1.0.0 .

# Build the CLI image
docker build \
  --build-arg SENTRY_RELEASE=v1.0.0 \
  --build-arg SENTRY_ENVIRONMENT=production \
  -f docker/cli/Dockerfile \
  -t ahmadfaryabkokab/hospital-care:v1.0.0-cli .
```

### Publishing

```bash
# Login to Docker Hub
docker login

# Push both images
docker push ahmadfaryabkokab/hospital-care:v1.0.0
docker push ahmadfaryabkokab/hospital-care:v1.0.0-cli
```

### Production Deployment

For production, update the `docker-compose.yml` to use published images instead of local builds:

```yaml
services:
  app:
    image: ahmadfaryabkokab/hospital-care:v1.0.0
    ports:
      - "8000:80"
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - APP_URL=https://your-hospital.com
    depends_on:
      - db

  cli:
    image: ahmadfaryabkokab/hospital-care:v1.0.0-cli
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    depends_on:
      - db

  db:
    image: mysql:8.0
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

### Sentry Integration

Both Dockerfiles accept build arguments for Sentry release tracking:

| Build Arg | Default | Description |
|-----------|---------|-------------|
| `SENTRY_RELEASE` | — | Release identifier (e.g., `v1.0.0`) |
| `SENTRY_ENVIRONMENT` | `production` | Environment name |

Set `SENTRY_LARAVEL_DSN` in your `.env` to enable error tracking.

---

## AI Setup (Laravel Boost & Skills)

This project is configured for AI-assisted development with [Laravel Boost](https://laravel.com/docs/boost), GitHub Copilot, and Claude Code.

### Laravel Boost

Laravel Boost provides an MCP server with tools designed for this Laravel application — database inspection, documentation search, error tracking, and more.

**Configuration:** [boost.json](boost.json)

```json
{
    "agents": ["claude_code", "copilot"],
    "guidelines": true,
    "mcp": true,
    "packages": ["filament/filament", "laravel/fortify"],
    "skills": ["wayfinder-development", "pest-testing", "..."]
}
```

**Available Boost Tools:**
| Tool | Purpose |
|------|---------|
| `search-docs` | Search version-specific docs for Laravel ecosystem packages |
| `database-schema` | Inspect table structure without reading migrations |
| `database-query` | Execute read-only database queries |
| `browser-logs` | Read browser console errors and exceptions |
| `get-absolute-url` | Get correct URLs for the running application |
| `application-info` | Get app environment and configuration details |
| `last-error` | Retrieve the most recent application error |

### AI Guidelines

The project ships with structured AI guidelines in `.ai/guidelines/`:

| File | Content |
|------|---------|
| `laravel.md` | Laravel conventions: controllers, models, routes, Form Requests |
| `filament.md` | Filament v4 patterns: resources, schemas, tables, pages |
| `frontend.md` | React + Inertia + TypeScript conventions |
| `database.md` | Migrations, factories, seeders, enums, query patterns |
| `testing.md` | Pest testing conventions and patterns |
| `product.md` | Product vision, design principles, compliance checklists |

### Skills

Skills are domain-specific knowledge packages activated automatically based on the task context. Located in `.github/skills/`:

| Skill | Activates When |
|-------|---------------|
| `pest-testing` | Writing, editing, or fixing tests |
| `inertia-react-development` | Creating React pages, forms, or navigation |
| `wayfinder-development` | Referencing backend routes in frontend components |
| `tailwindcss-development` | Styling with Tailwind CSS utilities |
| `livewire-development` | Working with Livewire components |
| `eloquent-best-practices` | Query optimization, relationship management |
| `laravel-controllers` | HTTP layer and controller patterns |
| `laravel-models` | Eloquent model patterns and database layer |
| `laravel-enums` | Backed enums and status values |
| `laravel-validation` | Form request validation rules |
| `laravel-policies` | Authorization and access control |
| `laravel-tdd` | Test-driven development workflow |
| `laravel-security-audit` | Security vulnerability analysis |
| `laravel-quality` | PHPStan, Pint, code quality |
| `md-expert` | PHC compliance, patient safety, medical workflows |
| `ui-implementation` | Frontend UI/UX implementation |
| `php-best-practices` | PHP 8.x standards, PSR, SOLID |

### Setting Up AI Assistance

**1. Install Laravel Boost:**
```bash
composer require laravel/boost --dev
```

**2. Enable MCP in your IDE:**
- For **VS Code / GitHub Copilot**: Boost auto-registers as an MCP server
- For **Claude Code**: Run `php artisan boost:install claude_code`

**3. Verify Skills:**
```bash
# Skills are automatically loaded from .github/skills/
# No additional setup required
```

**4. Verify Guidelines:**
```bash
# Guidelines are auto-loaded from .ai/guidelines/
# Product-specific rules in AGENTS.md are merged at runtime
ls .ai/guidelines/
```

### AGENTS.md

The [AGENTS.md](AGENTS.md) file contains comprehensive operational rules for AI agents, including:
- Git workflow and change control procedures
- Database conventions and migration patterns
- Filament v4 resource structure and testing patterns
- Frontend (React + Inertia) conventions
- Testing requirements (every change must include tests)
- PHC and HIPAA compliance guidelines
- Product guidelines and feature prioritization

---

## License

This project is licensed under the [MIT License](LICENSE).
