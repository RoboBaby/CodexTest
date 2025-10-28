<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\RedirectResponse;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\PromptSectionRepository;
use RuntimeException;

final class PromptSectionController
{
    public function __construct(private readonly PromptSectionRepository $sections)
    {
    }

    public function index(Request $request): Response
    {
        return view('sections/index', [
            'sections' => $this->sections->all(),
            'message' => $request->query('message'),
        ]);
    }

    public function create(Request $request): Response
    {
        return view('sections/form', [
            'action' => '/sections/create',
            'section' => ['key' => '', 'title' => '', 'description' => '', 'order_index' => 0, 'enabled' => 1],
            'errors' => [],
        ]);
    }

    public function store(Request $request): Response
    {
        $payload = $this->validate($request);
        if ($payload['errors']) {
            return view('sections/form', [
                'action' => '/sections/create',
                'section' => $payload['data'],
                'errors' => $payload['errors'],
            ]);
        }

        if ($this->sections->findByKey($payload['data']['key'])) {
            $payload['errors']['key'] = 'A section with this key already exists.';
            return view('sections/form', [
                'action' => '/sections/create',
                'section' => $payload['data'],
                'errors' => $payload['errors'],
            ]);
        }

        $this->sections->create($payload['data']);

        return new RedirectResponse('/sections?message=Section%20created');
    }

    public function edit(Request $request, array $params): Response
    {
        $section = $this->requireSection((int) $params['id']);

        return view('sections/form', [
            'action' => '/sections/' . $section['id'] . '/edit',
            'section' => $section,
            'errors' => [],
        ]);
    }

    public function update(Request $request, array $params): Response
    {
        $section = $this->requireSection((int) $params['id']);
        $payload = $this->validate($request, $section, false);
        if ($payload['errors']) {
            return view('sections/form', [
                'action' => '/sections/' . $section['id'] . '/edit',
                'section' => array_merge($section, $payload['data']),
                'errors' => $payload['errors'],
            ]);
        }

        $this->sections->update($section['id'], $payload['data']);

        return new RedirectResponse('/sections?message=Section%20updated');
    }

    private function requireSection(int $id): array
    {
        $section = $this->sections->find($id);
        if (!$section) {
            throw new RuntimeException('Section not found.');
        }

        return $section;
    }

    /**
     * @return array{data:array<string,mixed>,errors:array<string,string>}
     */
    private function validate(Request $request, array $defaults = [], bool $requireKey = true): array
    {
        $data = [
            'key' => trim((string) $request->input('key', $defaults['key'] ?? '')),
            'title' => trim((string) $request->input('title', $defaults['title'] ?? '')),
            'description' => (string) $request->input('description', $defaults['description'] ?? ''),
            'order_index' => (int) $request->input('order_index', $defaults['order_index'] ?? 0),
            'enabled' => $request->input('enabled', $defaults['enabled'] ?? 1) ? 1 : 0,
        ];

        $errors = [];
        if ($requireKey && $data['key'] === '') {
            $errors['key'] = 'Key is required.';
        }
        if ($data['title'] === '') {
            $errors['title'] = 'Title is required.';
        }
        if ($data['order_index'] < 0) {
            $errors['order_index'] = 'Order index must be zero or greater.';
        }

        return ['data' => $data, 'errors' => $errors];
    }
}
