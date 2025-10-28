<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Connection;
use PDO;

final class PromptSectionRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::pdo();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM prompt_section ORDER BY order_index ASC, id ASC');

        return $stmt->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM prompt_section WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $section = $stmt->fetch();

        return $section ?: null;
    }

    public function findByKey(string $key): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM prompt_section WHERE key = :key');
        $stmt->execute(['key' => $key]);
        $section = $stmt->fetch();

        return $section ?: null;
    }

    /**
     * @param array<string,mixed> $attributes
     */
    public function create(array $attributes): int
    {
        $now = $this->now();
        $stmt = $this->pdo->prepare('INSERT INTO prompt_section (key, title, description, order_index, enabled, created_at, updated_at)
            VALUES (:key, :title, :description, :order_index, :enabled, :created_at, :updated_at)');
        $stmt->execute([
            'key' => $attributes['key'],
            'title' => $attributes['title'] ?? null,
            'description' => $attributes['description'] ?? null,
            'order_index' => (int) ($attributes['order_index'] ?? 0),
            'enabled' => !empty($attributes['enabled']) ? 1 : 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * @param array<string,mixed> $attributes
     */
    public function update(int $id, array $attributes): void
    {
        $stmt = $this->pdo->prepare('UPDATE prompt_section SET title = :title, description = :description, order_index = :order_index, enabled = :enabled, updated_at = :updated_at WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'title' => $attributes['title'] ?? null,
            'description' => $attributes['description'] ?? null,
            'order_index' => (int) ($attributes['order_index'] ?? 0),
            'enabled' => !empty($attributes['enabled']) ? 1 : 0,
            'updated_at' => $this->now(),
        ]);
    }

    private function now(): string
    {
        return (new \DateTimeImmutable())->format('Y-m-d H:i:s');
    }
}
