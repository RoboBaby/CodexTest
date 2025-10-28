<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Connection;
use PDO;
use RuntimeException;

final class PromptVersionRepository
{
    public const STATUSES = ['draft', 'active', 'archived'];

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
        $stmt = $this->pdo->query('SELECT * FROM prompt_version ORDER BY created_at DESC, id DESC');

        return $stmt->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM prompt_version WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $version = $stmt->fetch();

        return $version ?: null;
    }

    /**
     * @param array<string,mixed> $attributes
     */
    public function create(array $attributes): int
    {
        $status = $attributes['status'];
        if (!in_array($status, self::STATUSES, true)) {
            throw new RuntimeException('Invalid status value.');
        }

        $now = $this->now();
        $stmt = $this->pdo->prepare('INSERT INTO prompt_version (prompt_name, version_label, status, notes, created_at, updated_at)
            VALUES (:prompt_name, :version_label, :status, :notes, :created_at, :updated_at)');
        $stmt->execute([
            'prompt_name' => $attributes['prompt_name'],
            'version_label' => $attributes['version_label'],
            'status' => $status,
            'notes' => $attributes['notes'] ?? null,
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
        $status = $attributes['status'];
        if (!in_array($status, self::STATUSES, true)) {
            throw new RuntimeException('Invalid status value.');
        }

        $stmt = $this->pdo->prepare('UPDATE prompt_version SET prompt_name = :prompt_name, version_label = :version_label, status = :status, notes = :notes, updated_at = :updated_at WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'prompt_name' => $attributes['prompt_name'],
            'version_label' => $attributes['version_label'],
            'status' => $status,
            'notes' => $attributes['notes'] ?? null,
            'updated_at' => $this->now(),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM prompt_version WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function setStatus(int $id, string $status): void
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw new RuntimeException('Invalid status value.');
        }

        $stmt = $this->pdo->prepare('UPDATE prompt_version SET status = :status, updated_at = :updated_at WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'status' => $status,
            'updated_at' => $this->now(),
        ]);
    }

    public function duplicate(int $id, string $newLabel): int
    {
        $original = $this->find($id);
        if (!$original) {
            throw new RuntimeException('Version not found.');
        }

        $copyId = $this->create([
            'prompt_name' => $original['prompt_name'],
            'version_label' => $newLabel,
            'status' => 'draft',
            'notes' => $original['notes'],
        ]);

        $this->pdo->prepare('INSERT INTO prompt_line (version_id, section_id, order_index, enabled, content, created_at, updated_at)
                SELECT :new_version_id, section_id, order_index, enabled, content, :created_at, :updated_at
                FROM prompt_line WHERE version_id = :original_version_id')
            ->execute([
                'new_version_id' => $copyId,
                'original_version_id' => $id,
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]);

        return $copyId;
    }

    private function now(): string
    {
        return (new \DateTimeImmutable())->format('Y-m-d H:i:s');
    }
}
