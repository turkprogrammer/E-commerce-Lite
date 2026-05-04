# AGENTS.md

## Quick Start

```bash
composer install
docker compose up -d
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## Commands

| Command | Purpose |
|---------|---------|
| `vendor/bin/phpstan analyse` | Static analysis (Level 6) |
| `vendor/bin/rector process --dry-run` | Check refactor changes |
| `vendor/bin/phpunit` | All tests |
| `vendor/bin/phpunit --testsuite Application` | Unit tests only |
| `vendor/bin/phpunit --testsuite Functional` | Integration tests |
| `vendor/bin/phpcs src/` | Code style (PSR-12) |

## Project Structure

- **src/Domain/** — Entities (Product, Category, Cart, CartItem, Order, OrderItem, Payment)
- **src/Domain/Port/** — Repository interfaces
- **src/Application/** — Use cases
- **src/Infrastructure/** — Doctrine repositories
- **tests/** — Application (unit) and Functional (integration) suites

## Important Quirks

- **PHPStan Level**: config says `level: 6`, NOT 9 as some docs claim
- **Mapping**: Uses PHP 8 Attributes, NOT XML
- **Tests**: SQLite in-memory (`DATABASE_URL` in phpunit.dist.xml), not PostgreSQL
- **Domain**: Entities use `Doctrine\ORM\Mapping` attributes — not truly decoupled from ORM

## Verified Sources

- `phpstan.dist.neon` — actual PHPStan config
- `phpunit.dist.xml` — test configuration
- `config/packages/doctrine.yaml` — ORM mapping driver