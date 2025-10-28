<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Connection;
use PDO;
use RuntimeException;

final class PromptLineRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::pdo();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function forVersion(int $versionId, ?int $sectionId = null, bool $includeDisabled = true): array
    {
        $sql = 'SELECT l.*, s.key as section_key, s.title as section_title, s.order_index as section_order
                FROM prompt_line l
                INNER JOIN prompt_section s ON s.id = l.section_id
                WHERE l.version_id = :version_id';
        $params = ['version_id' => $versionId];

        if ($sectionId !== null) {
            $sql .= ' AND l.section_id = :section_id';
            $params['section_id'] = $sectionId;
        }

        if (!$includeDisabled) {
            $sql .= ' AND l.enabled = 1 AND s.enabled = 1';
        }

        $sql .= ' ORDER BY s.order_index ASC, l.order_index ASC, l.id ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM prompt_line WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $line = $stmt->fetch();

        return $line ?: null;
    }

    /**
     * @param array<string,mixed> $attributes
     */
    public function create(array $attributes): int
    {
        $order = $attributes['order_index'] ?? $this->nextOrder((int) $attributes['version_id']);
        $now = $this->now();
        $stmt = $this->pdo->prepare('INSERT INTO prompt_line (version_id, section_id, order_index, enabled, content, created_at, updated_at)
            VALUES (:version_id, :section_id, :order_index, :enabled, :content, :created_at, :updated_at)');
        $stmt->execute([
            'version_id' => (int) $attributes['version_id'],
            'section_id' => (int) $attributes['section_id'],
            'order_index' => (int) $order,
            'enabled' => !empty($attributes['enabled']) ? 1 : 0,
            'content' => $attributes['content'],
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
        $stmt = $this->pdo->prepare('UPDATE prompt_line SET section_id = :section_id, order_index = :order_index, enabled = :enabled, content = :content, updated_at = :updated_at WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'section_id' => (int) $attributes['section_id'],
            'order_index' => (int) ($attributes['order_index'] ?? 0),
            'enabled' => !empty($attributes['enabled']) ? 1 : 0,
            'content' => $attributes['content'],
            'updated_at' => $this->now(),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM prompt_line WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function move(int $id, int $direction): void
    {
        $line = $this->find($id);
        if (!$line) {
            throw new RuntimeException('Line not found.');
        }

        $operator = $direction < 0 ? '<' : '>';
        $order = (int) $line['order_index'];
        $comparison = $direction < 0 ? 'DESC' : 'ASC';
        $stmt = $this->pdo->prepare("SELECT * FROM prompt_line WHERE version_id = :version_id AND order_index {$operator} :order_index ORDER BY order_index {$comparison} LIMIT 1");
        $stmt->execute([
            'version_id' => $line['version_id'],
            'order_index' => $order,
        ]);
        $swap = $stmt->fetch();

        if (!$swap) {
            return;
        }

        $this->pdo->beginTransaction();
        try {
            $this->updateOrder((int) $line['id'], (int) $swap['order_index']);
            $this->updateOrder((int) $swap['id'], $order);
            $this->pdo->commit();
        } catch (RuntimeException $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function resequence(int $versionId): void
    {
        $stmt = $this->pdo->prepare('SELECT id FROM prompt_line WHERE version_id = :version_id ORDER BY order_index ASC, id ASC');
        $stmt->execute(['version_id' => $versionId]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $order = 1;
        $update = $this->pdo->prepare('UPDATE prompt_line SET order_index = :order_index, updated_at = :updated_at WHERE id = :id');
        foreach ($ids as $id) {
            $update->execute([
                'order_index' => $order++,
                'updated_at' => $this->now(),
                'id' => $id,
            ]);
        }
    }

    private function updateOrder(int $id, int $order): void
    {
        $stmt = $this->pdo->prepare('UPDATE prompt_line SET order_index = :order_index, updated_at = :updated_at WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'order_index' => $order,
            'updated_at' => $this->now(),
        ]);
    }

    private function nextOrder(int $versionId): int
    {
        $stmt = $this->pdo->prepare('SELECT MAX(order_index) FROM prompt_line WHERE version_id = :version_id');
        $stmt->execute(['version_id' => $versionId]);
        $max = $stmt->fetchColumn();

        return ((int) $max) + 1;
    }

    private function now(): string
    {
        return (new \DateTimeImmutable())->format('Y-m-d H:i:s');
    }
}
