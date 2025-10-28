# AskGVT Prompt Editor

A Laravel-based prompt management tool for editing and versioning system prompts.

## Setup

1. Install dependencies: `composer install`
2. Copy `.env.example` to `.env` and adjust PostgreSQL settings.
3. Generate an app key: `php artisan key:generate`
4. Run migrations and seed the default sections:
   ```bash
   php artisan migrate --seed
   ```
5. Serve the application: `php artisan serve`

## Features

- CRUD management for prompt versions, sections, and lines
- Section-based filtering and ordering
- Prompt line reordering per version/section
- Duplicate prompt versions for iteration
- API endpoint (`GET /api/prompt-versions/{id}/render`) to fetch the rendered prompt text

## Rendering

The renderer combines enabled lines grouped by section and produces markdown-like output for downstream services.
