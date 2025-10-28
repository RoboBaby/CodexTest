<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;
use RuntimeException;

final class Connection
{
    private static ?PDO $pdo = null;

    /** @var array<string,mixed>|null */
    private static ?array $override = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $config = self::$override ?? self::config();
        $driver = $config['driver'] ?? null;

        if ($driver === null) {
            throw new RuntimeException('Database driver not configured.');
        }

        try {
            if ($driver === 'sqlite') {
                $database = $config['database'] ?? ':memory:';
                if ($database !== ':memory:') {
                    if (!str_starts_with($database, '/')) {
                        $database = BASE_PATH . '/' . ltrim($database, '/');
                    }

                    $dir = dirname($database);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                }

                $pdo = new PDO('sqlite:' . $database);
            } elseif ($driver === 'pgsql') {
                $host = $config['host'] ?? '127.0.0.1';
                $port = $config['port'] ?? '5432';
                $dbname = $config['database'] ?? '';
                $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $dbname);
                $pdo = new PDO($dsn, (string) ($config['username'] ?? ''), (string) ($config['password'] ?? ''));
            } else {
                throw new RuntimeException('Unsupported database driver: ' . $driver);
            }
        } catch (PDOException $exception) {
            throw new RuntimeException('Failed to connect to database: ' . $exception->getMessage(), 0, $exception);
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        self::$pdo = $pdo;

        return self::$pdo;
    }

    public static function reset(): void
    {
        self::$pdo = null;
    }

    /**
     * @param array<string,mixed>|null $config
     */
    public static function override(?array $config): void
    {
        self::$override = $config;
        self::reset();
    }

    /**
     * @return array<string,mixed>
     */
    private static function config(): array
    {
        $default = config('database.default');
        $connections = config('database.connections', []);

        if (!is_string($default) || !isset($connections[$default])) {
            throw new RuntimeException('Database configuration not found.');
        }

        return $connections[$default];
    }
}
