<?php

declare(strict_types=1);

namespace App\View;

use App\Http\Response;
use RuntimeException;

final class View
{
    public function __construct(private readonly string $basePath)
    {
    }

    /**
     * @param array<string,mixed> $data
     */
    public function render(string $template, array $data = []): Response
    {
        $path = rtrim($this->basePath, '/') . '/' . $template . '.php';
        if (!is_file($path)) {
            throw new RuntimeException(sprintf('View [%s] not found.', $template));
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include $path;
        $content = (string) ob_get_clean();

        return new Response($content);
    }
}
