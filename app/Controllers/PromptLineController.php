<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\RedirectResponse;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\PromptLineRepository;
use App\Repositories\PromptSectionRepository;
use App\Repositories\PromptVersionRepository;
use RuntimeException;

final class PromptLineController
{
    public function __construct(
        private readonly PromptVersionRepository $versions,
        private readonly PromptSectionRepository $sections,
        private readonly PromptLineRepository $lines
    ) {
    }

    public function create(Request $request, array $params): Response
    {
        $version = $this->requireVersion((int) $params['version']);
        $sectionId = $request->query('section') ? (int) $request->query('section') : null;

        return view('lines/form', [
            'action' => '/versions/' . $version['id'] . '/lines/create',
            'version' => $version,
            'sections' => $this->sections->all(),
            'line' => ['section_id' => $sectionId, 'content' => '', 'order_index' => null, 'enabled' => 1],
            'errors' => [],
        ]);
    }

    public function store(Request $request, array $params): Response
    {
        $version = $this->requireVersion((int) $params['version']);
        $payload = $this->validate($request);
        if ($payload['errors']) {
            return view('lines/form', [
                'action' => '/versions/' . $version['id'] . '/lines/create',
                'version' => $version,
                'sections' => $this->sections->all(),
                'line' => $payload['data'],
                'errors' => $payload['errors'],
            ]);
        }

        $payload['data']['version_id'] = $version['id'];
        $this->lines->create($payload['data']);
        $this->lines->resequence($version['id']);

        return new RedirectResponse('/versions/' . $version['id'] . '?message=Line%20added');
    }

    public function edit(Request $request, array $params): Response
    {
        $line = $this->requireLine((int) $params['id']);
        $version = $this->requireVersion((int) $line['version_id']);

        return view('lines/form', [
            'action' => '/lines/' . $line['id'] . '/edit',
            'version' => $version,
            'sections' => $this->sections->all(),
            'line' => $line,
            'errors' => [],
        ]);
    }

    public function update(Request $request, array $params): Response
    {
        $line = $this->requireLine((int) $params['id']);
        $version = $this->requireVersion((int) $line['version_id']);
        $payload = $this->validate($request, $line);
        if ($payload['errors']) {
            return view('lines/form', [
                'action' => '/lines/' . $line['id'] . '/edit',
                'version' => $version,
                'sections' => $this->sections->all(),
                'line' => array_merge($line, $payload['data']),
                'errors' => $payload['errors'],
            ]);
        }

        $this->lines->update($line['id'], $payload['data']);
        $this->lines->resequence($version['id']);

        return new RedirectResponse('/versions/' . $version['id'] . '?message=Line%20updated');
    }

    public function destroy(Request $request, array $params): Response
    {
        $line = $this->requireLine((int) $params['id']);
        $versionId = (int) $line['version_id'];
        $this->lines->delete($line['id']);
        $this->lines->resequence($versionId);

        return new RedirectResponse('/versions/' . $versionId . '?message=Line%20deleted');
    }

    public function move(Request $request, array $params): Response
    {
        $line = $this->requireLine((int) $params['id']);
        $direction = $request->input('direction');
        $delta = $direction === 'up' ? -1 : 1;
        $this->lines->move($line['id'], $delta);

        return new RedirectResponse('/versions/' . $line['version_id'] . '?message=Line%20reordered');
    }

    private function requireVersion(int $id): array
    {
        $version = $this->versions->find($id);
        if (!$version) {
            throw new RuntimeException('Prompt version not found.');
        }

        return $version;
    }

    private function requireLine(int $id): array
    {
        $line = $this->lines->find($id);
        if (!$line) {
            throw new RuntimeException('Prompt line not found.');
        }

        return $line;
    }

    /**
     * @return array{data:array<string,mixed>,errors:array<string,string>}
     */
    private function validate(Request $request, array $defaults = []): array
    {
        $data = [
            'section_id' => $request->input('section_id', $defaults['section_id'] ?? null),
            'order_index' => $request->input('order_index', $defaults['order_index'] ?? null),
            'enabled' => $request->input('enabled', $defaults['enabled'] ?? 1) ? 1 : 0,
            'content' => trim((string) $request->input('content', $defaults['content'] ?? '')),
        ];

        if ($data['order_index'] === '' || $data['order_index'] === null) {
            $data['order_index'] = null;
        } else {
            $data['order_index'] = (int) $data['order_index'];
        }

        $errors = [];
        if ($data['section_id'] === null || !$this->sections->find((int) $data['section_id'])) {
            $errors['section_id'] = 'Please select a valid section.';
        }
        if ($data['content'] === '') {
            $errors['content'] = 'Content cannot be empty.';
        }
        if ($data['order_index'] !== null && $data['order_index'] < 0) {
            $errors['order_index'] = 'Order index must be zero or greater.';
        }

        return ['data' => $data, 'errors' => $errors];
    }
}
