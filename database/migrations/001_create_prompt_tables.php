<?php

declare(strict_types=1);

use App\Database\Migrations\Migration;

return new class extends Migration {
    public function up(\PDO $pdo): void
    {
        $driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
        if ($driver === 'pgsql') {
            $pdo->exec('CREATE TABLE IF NOT EXISTS prompt_version (
                id BIGSERIAL PRIMARY KEY,
                prompt_name TEXT NOT NULL,
                version_label TEXT NOT NULL,
                status TEXT NOT NULL,
                notes TEXT NULL,
                created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
            )');

            $pdo->exec('CREATE TABLE IF NOT EXISTS prompt_section (
                id BIGSERIAL PRIMARY KEY,
                key TEXT NOT NULL UNIQUE,
                title TEXT NULL,
                description TEXT NULL,
                order_index INTEGER NOT NULL DEFAULT 0,
                enabled BOOLEAN NOT NULL DEFAULT TRUE,
                created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
            )');

            $pdo->exec('CREATE TABLE IF NOT EXISTS prompt_line (
                id BIGSERIAL PRIMARY KEY,
                version_id BIGINT NOT NULL REFERENCES prompt_version(id) ON DELETE CASCADE,
                section_id BIGINT NOT NULL REFERENCES prompt_section(id) ON DELETE RESTRICT,
                order_index INTEGER NOT NULL DEFAULT 0,
                enabled BOOLEAN NOT NULL DEFAULT TRUE,
                content TEXT NOT NULL,
                created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
            )');
        } else {
            $pdo->exec('CREATE TABLE IF NOT EXISTS prompt_version (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                prompt_name TEXT NOT NULL,
                version_label TEXT NOT NULL,
                status TEXT NOT NULL,
                notes TEXT NULL,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )');

            $pdo->exec('CREATE TABLE IF NOT EXISTS prompt_section (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                key TEXT NOT NULL UNIQUE,
                title TEXT NULL,
                description TEXT NULL,
                order_index INTEGER NOT NULL DEFAULT 0,
                enabled INTEGER NOT NULL DEFAULT 1,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )');

            $pdo->exec('CREATE TABLE IF NOT EXISTS prompt_line (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                version_id INTEGER NOT NULL,
                section_id INTEGER NOT NULL,
                order_index INTEGER NOT NULL DEFAULT 0,
                enabled INTEGER NOT NULL DEFAULT 1,
                content TEXT NOT NULL,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(version_id) REFERENCES prompt_version(id) ON DELETE CASCADE,
                FOREIGN KEY(section_id) REFERENCES prompt_section(id) ON DELETE RESTRICT
            )');
        }
    }

    public function down(\PDO $pdo): void
    {
        $pdo->exec('DROP TABLE IF EXISTS prompt_line');
        $pdo->exec('DROP TABLE IF EXISTS prompt_section');
        $pdo->exec('DROP TABLE IF EXISTS prompt_version');
    }
};
