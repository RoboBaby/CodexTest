<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\RedirectResponse;
use App\Http\Request;
use App\Http\Response;
use App\Repositories\PromptLineRepository;
use App\Repositories\PromptSectionRepository;
use App\Repositories\PromptVersionRepository;
use App\Services\PromptRenderer;
use RuntimeException;

final class PromptVersionController
{
    public function __construct(
        private readonly PromptVersionRepository $versions,
        private readonly PromptSectionRepository $sections,
        private readonly PromptLineRepository $lines,
        private readonly PromptRenderer $renderer,
    ) {
    }

    public function index(Request $request): Response
    {
        $versions = $this->versions->all();

        return view('versions/index', [
            'versions' => $versions,
            'statuses' => PromptVersionRepository::STATUSES,
            'message' => $request->query('message'),
        ]);
    }

    public function create(Request $request): Response
    {
        return view('versions/form', [
            'action' => '/versions/create',
            'version' => ['prompt_name' => '', 'version_label' => '', 'status' => 'draft', 'notes' => ''],
            'statuses' => PromptVersionRepository::STATUSES,
            'errors' => [],
        ]);
    }

    public function store(Request $request): Response
    {
        $payload = $this->validate($request);
        if ($payload['errors']) {
            return view('versions/form', [
                'action' => '/versions/create',
                'version' => $payload['data'],
                'statuses' => PromptVersionRepository::STATUSES,
                'errors' => $payload['errors'],
            ]);
        }

        $id = $this->versions->create($payload['data']);

        return new RedirectResponse('/versions/' . $id . '?message=Version%20created');
    }

    public function edit(Request $request, array $params): Response
    {
        $version = $this->requireVersion((int) $params['id']);

        return view('versions/form', [
            'action' => '/versions/' . $version['id'] . '/edit',
            'version' => $version,
            'statuses' => PromptVersionRepository::STATUSES,
            'errors' => [],
        ]);
    }

    public function update(Request $request, array $params): Response
    {
        $version = $this->requireVersion((int) $params['id']);
        $payload = $this->validate($request, $version);
        if ($payload['errors']) {
            return view('versions/form', [
                'action' => '/versions/' . $version['id'] . '/edit',
                'version' => array_merge($version, $payload['data']),
                'statuses' => PromptVersionRepository::STATUSES,
                'errors' => $payload['errors'],
            ]);
        }

        $this->versions->update($version['id'], $payload['data']);

        return new RedirectResponse('/versions/' . $version['id'] . '?message=Version%20updated');
    }

    public function destroy(Request $request, array $params): Response
    {
        $version = $this->requireVersion((int) $params['id']);
        $this->versions->delete($version['id']);

        return new RedirectResponse('/versions?message=Version%20deleted');
    }

    public function show(Request $request, array $params): Response
    {
        $version = $this->requireVersion((int) $params['id']);
        $sectionId = $request->query('section') ? (int) $request->query('section') : null;
        $sections = $this->sections->all();
        $lines = $this->lines->forVersion($version['id'], $sectionId, true);

        return view('versions/show', [
            'version' => $version,
            'sections' => $sections,
            'selectedSection' => $sectionId,
            'lines' => $lines,
            'message' => $request->query('message'),
        ]);
    }

    public function updateStatus(Request $request, array $params): Response
    {
        $version = $this->requireVersion((int) $params['id']);
        $status = $request->input('status');
        if (!is_string($status) || !in_array($status, PromptVersionRepository::STATUSES, true)) {
            return view('versions/show', [
                'version' => $version,
                'sections' => $this->sections->all(),
                'selectedSection' => null,
                'lines' => $this->lines->forVersion($version['id'], null, true),
                'message' => 'Invalid status value provided.',
            ]);
        }

        $this->versions->setStatus($version['id'], $status);

        return new RedirectResponse('/versions/' . $version['id'] . '?message=Status%20updated');
    }

    public function duplicate(Request $request, array $params): Response
    {
        $version = $this->requireVersion((int) $params['id']);
        $label = trim((string) $request->input('version_label', ''));
        if ($label === '') {
            return new RedirectResponse('/versions/' . $version['id'] . '?message=Provide%20a%20label%20for%20duplication');
        }

        $copyId = $this->versions->duplicate($version['id'], $label);

        return new RedirectResponse('/versions/' . $copyId . '?message=Version%20duplicated');
    }

    public function renderPrompt(Request $request, array $params): Response
    {
        $version = $this->requireVersion((int) $params['id']);
        $text = $this->renderer->renderText($version['id']);

        return Response::json([
            'prompt_name' => $version['prompt_name'],
            'version_label' => $version['version_label'],
            'status' => $version['status'],
            'rendered_prompt' => $text,
        ]);
    }

    private function requireVersion(int $id): array
    {
        $version = $this->versions->find($id);
        if (!$version) {
            throw new RuntimeException('Prompt version not found.');
        }

        return $version;
    }

    /**
     * @return array{data:array<string,mixed>,errors:array<string,string>}
     */
    private function validate(Request $request, array $defaults = []): array
    {
        $data = [
            'prompt_name' => trim((string) $request->input('prompt_name', $defaults['prompt_name'] ?? '')),
            'version_label' => trim((string) $request->input('version_label', $defaults['version_label'] ?? '')),
            'status' => (string) $request->input('status', $defaults['status'] ?? 'draft'),
            'notes' => (string) $request->input('notes', $defaults['notes'] ?? ''),
        ];

        $errors = [];
        if ($data['prompt_name'] === '') {
            $errors['prompt_name'] = 'Prompt name is required.';
        }
        if ($data['version_label'] === '') {
            $errors['version_label'] = 'Version label is required.';
        }
        if (!in_array($data['status'], PromptVersionRepository::STATUSES, true)) {
            $errors['status'] = 'Status must be one of: ' . implode(', ', PromptVersionRepository::STATUSES);
        }

        return ['data' => $data, 'errors' => $errors];
    }
}
