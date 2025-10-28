<?php

declare(strict_types=1);

namespace App\Http;

class Response
{
    /** @param array<string,string> $headers */
    public function __construct(
        private string $content,
        private int $status = 200,
        private array $headers = []
    ) {
    }

    public static function json(array $data, int $status = 200): self
    {
        return new self(json_encode($data, JSON_PRETTY_PRINT), $status, ['Content-Type' => 'application/json']);
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $key => $value) {
            header(sprintf('%s: %s', $key, $value));
        }

        echo $this->content;
    }

    public function content(): string
    {
        return $this->content;
    }
}
