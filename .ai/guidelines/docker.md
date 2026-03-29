# Docker Conventions

All commands must run inside the Docker `cli` container. Never execute PHP, Composer, Artisan, Node, npm, or Bun commands directly on the host machine.

---

## Container Reference

| Service | Purpose |
|---------|---------|
| `cli` | All CLI operations: Artisan, Composer, Pest, npm/Bun, tinker |
| `app` | Nginx + PHP-FPM (serves HTTP requests) |
| `db` | MySQL 8.0 database |
| `pma` | phpMyAdmin (database UI) |

---

## Running Commands

Use `docker compose exec cli` to run commands inside the CLI container:

```bash
# Artisan
docker compose exec cli php artisan migrate
docker compose exec cli php artisan test --compact
docker compose exec cli php artisan make:model SomeModel --no-interaction

# Composer
docker compose exec cli composer install
docker compose exec cli composer require some/package

# Pest / Tests
docker compose exec cli php artisan test --compact
docker compose exec cli php artisan test --compact --filter=testName
docker compose exec cli php artisan test --compact tests/Feature/SomeTest.php

# Pint (code formatting)
docker compose exec cli vendor/bin/pint --dirty

# Node / npm / Bun
docker compose exec cli npm run build
docker compose exec cli npm run dev
docker compose exec cli bun install
docker compose exec cli bun run build

# Tinker
docker compose exec cli php artisan tinker --execute "User::count()"
```

---

## Forbidden on Host

The following must **never** run directly on the host:

- `php artisan *`
- `composer *`
- `vendor/bin/pint`
- `npm *` / `bun *`
- `phpunit` / `pest`

Always prefix with `docker compose exec cli`.

---

## Key Reminders

- The working directory inside the container is `/var/www/html` (project root is already mounted).
- Use `docker compose exec cli` (not `docker compose run`) to avoid creating orphan containers.
- If the container is not running, start it first with `docker compose up -d`.
- For interactive commands (e.g., tinker without `--execute`), add the `-it` flag: `docker compose exec -it cli php artisan tinker`.
- Database hostname inside containers is `db`, not `localhost` or `127.0.0.1`.
