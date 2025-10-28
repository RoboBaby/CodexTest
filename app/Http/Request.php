<?php

declare(strict_types=1);

namespace App\Http;

final class Request
{
    /** @param array<string,mixed> $query */
    /** @param array<string,mixed> $body */
    /** @param array<string,mixed> $server */
    public function __construct(
        private array $query,
        private array $body,
        private array $server,
    ) {
    }

    public static function capture(): self
    {
        return new self($_GET, $_POST, $_SERVER);
    }

    public function method(): string
    {
        return strtoupper((string) ($this->server['REQUEST_METHOD'] ?? 'GET'));
    }

    public function path(): string
    {
        $uri = (string) ($this->server['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH);

        return $path === null ? '/' : ($path === '' ? '/' : $path);
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query($key, $default);
    }

    /**
     * @param array<int,string> $keys
     * @return array<string,mixed>
     */
    public function only(array $keys): array
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->input($key);
        }

        return $values;
    }

    /**
     * @return array<string,mixed>
     */
    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }
}
