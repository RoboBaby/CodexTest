<?php

declare(strict_types=1);

namespace App\Routing;

use App\Http\Request;
use App\Http\Response;
use RuntimeException;

final class Router
{
    /** @var array<int,array{method:string,pattern:string,handler:callable}> */
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->addRoute('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->addRoute('POST', $pattern, $handler);
    }

    private function addRoute(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = compact('method', 'pattern', 'handler');
    }

    public function dispatch(Request $request): Response
    {
        $path = rtrim($request->path(), '/') ?: '/';
        $method = $request->method();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $regex = $this->toRegex($route['pattern']);
            if (preg_match($regex, $path, $matches)) {
                $params = array_filter(
                    $matches,
                    fn($key) => !is_int($key),
                    ARRAY_FILTER_USE_KEY
                );
                $response = ($route['handler'])($request, $params);

                if ($response instanceof Response) {
                    return $response;
                }

                if (is_string($response)) {
                    return new Response($response);
                }

                throw new RuntimeException('Route handler must return a Response or string.');
            }
        }

        return new Response('Not Found', 404);
    }

    private function toRegex(string $pattern): string
    {
        $pattern = rtrim($pattern, '/') ?: '/';
        $escaped = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
        $escaped = str_replace('/', '\/', $escaped ?? $pattern);

        return '#^' . $escaped . '$#';
    }
}
