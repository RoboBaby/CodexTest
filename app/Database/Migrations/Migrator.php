<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use App\Database\Connection;
use PDO;
use RuntimeException;

final class Migrator
{
    public function __construct(private readonly string $path)
    {
    }

    public function migrate(): void
    {
        $pdo = Connection::pdo();
        $this->ensureMigrationsTable($pdo);

        $files = glob(rtrim($this->path, '/') . '/*.php') ?: [];
        sort($files);

        foreach ($files as $file) {
            $name = basename($file);
            if ($this->hasRun($pdo, $name)) {
                continue;
            }

            $migration = $this->resolve($file);
            $pdo->beginTransaction();
            try {
                $migration->up($pdo);
                $this->markAsRun($pdo, $name);
                $pdo->commit();
            } catch (RuntimeException $exception) {
                $pdo->rollBack();
                throw $exception;
            }
        }
    }

    public function rollbackLast(): void
    {
        $pdo = Connection::pdo();
        $this->ensureMigrationsTable($pdo);
        $last = $pdo->query('SELECT name FROM migrations ORDER BY id DESC LIMIT 1');
        if (!$last) {
            return;
        }

        $row = $last->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return;
        }

        $name = (string) $row['name'];
        $file = rtrim($this->path, '/') . '/' . $name;
        if (!is_file($file)) {
            return;
        }

        $migration = $this->resolve($file);
        $pdo->beginTransaction();
        try {
            $migration->down($pdo);
            $stmt = $pdo->prepare('DELETE FROM migrations WHERE name = :name');
            $stmt->execute(['name' => $name]);
            $pdo->commit();
        } catch (RuntimeException $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }

    private function resolve(string $file): Migration
    {
        $migration = require $file;
        if (!$migration instanceof Migration) {
            throw new RuntimeException(sprintf('Migration file %s must return a Migration instance.', $file));
        }

        return $migration;
    }

    private function ensureMigrationsTable(PDO $pdo): void
    {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'pgsql') {
            $pdo->exec('CREATE TABLE IF NOT EXISTS migrations (id BIGSERIAL PRIMARY KEY, name TEXT NOT NULL UNIQUE, ran_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP)');
        } else {
            $pdo->exec('CREATE TABLE IF NOT EXISTS migrations (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL UNIQUE, ran_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)');
        }
    }

    private function hasRun(PDO $pdo, string $name): bool
    {
        $stmt = $pdo->prepare('SELECT 1 FROM migrations WHERE name = :name');
        $stmt->execute(['name' => $name]);

        return (bool) $stmt->fetchColumn();
    }

    private function markAsRun(PDO $pdo, string $name): void
    {
        $stmt = $pdo->prepare('INSERT INTO migrations (name) VALUES (:name)');
        $stmt->execute(['name' => $name]);
    }
}
