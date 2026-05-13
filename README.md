# Scalable Ecommerce

E-commerce platform built as an evolving monolith. Local development runs the
Laravel application directly with PHP's built-in server.

## Requirements

- PHP 8.5
- Composer 2

## Quick start

```bash
cd app
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

The application exposes a health endpoint at `GET /up`.

## Commit convention

This repository follows Conventional Commits:
`feat | fix | docs | style | refactor | perf | test | chore | ci | build`.
