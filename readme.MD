# AskGVT Prompt Editor

A lightweight PHP prompt management tool for editing and versioning AskGVT system prompts without relying on external Composer packages.

## Setup

1. Copy `.env.example` to `.env` and adjust the database settings. SQLite is used by default and requires no additional configuration.
2. Run the database migrations and seed the default prompt sections:
   ```bash
   php artisan migrate --seed
   ```
3. (Optional) Execute the built-in test suite:
   ```bash
   php artisan test --fresh
   ```
4. Serve the application using PHP's built-in web server:
   ```bash
   php -S localhost:8000 -t public
   ```

## Features

- CRUD management for prompt versions, sections, and lines via a simple web UI
- Section-based filtering and ordering controls for prompt lines
- Duplicate prompt versions to iterate on variants quickly
- API endpoint (`GET /api/prompts/{id}`) that returns the rendered prompt text with metadata
- Standalone artisan-style CLI for migrations, seeding, and tests

## Rendering

The renderer groups enabled sections and lines, producing a markdown-friendly prompt body that downstream services can consume directly.
