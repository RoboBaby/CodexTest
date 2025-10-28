<?php

declare(strict_types=1);

use App\Support\Env;

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null)
    {
        $segments = explode('.', $key);
        $file = array_shift($segments);
        $path = BASE_PATH . '/config/' . $file . '.php';

        if (!is_file($path)) {
            return $default;
        }

        $config = require $path;

        foreach ($segments as $segment) {
            if (is_array($config) && array_key_exists($segment, $config)) {
                $config = $config[$segment];
            } else {
                return $default;
            }
        }

        return $config;
    }
}

if (!function_exists('view')) {
    /**
     * @param array<string,mixed> $data
     */
    function view(string $template, array $data = []): App\Http\Response
    {
        $factory = new App\View\View(BASE_PATH . '/resources/views');

        return $factory->render($template, $data);
    }
}
