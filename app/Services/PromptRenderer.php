<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PromptLineRepository;
use App\Repositories\PromptSectionRepository;
use App\Repositories\PromptVersionRepository;
use RuntimeException;

final class PromptRenderer
{
    public function __construct(
        private readonly PromptVersionRepository $versions,
        private readonly PromptSectionRepository $sections,
        private readonly PromptLineRepository $lines,
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    public function structuredPrompt(int $versionId): array
    {
        $version = $this->versions->find($versionId);
        if (!$version) {
            throw new RuntimeException('Prompt version not found.');
        }

        $sections = $this->sections->all();
        $lines = $this->lines->forVersion($versionId, null, false);

        $grouped = [];
        foreach ($sections as $section) {
            if ((int) $section['enabled'] !== 1) {
                continue;
            }

            $grouped[$section['id']] = [
                'section' => $section,
                'lines' => [],
            ];
        }

        foreach ($lines as $line) {
            $sectionId = (int) $line['section_id'];
            if (!isset($grouped[$sectionId])) {
                continue;
            }

            $grouped[$sectionId]['lines'][] = $line;
        }

        return [
            'version' => $version,
            'sections' => array_values($grouped),
        ];
    }

    public function renderText(int $versionId): string
    {
        $prompt = $this->structuredPrompt($versionId);
        $lines = [];

        foreach ($prompt['sections'] as $bundle) {
            $section = $bundle['section'];
            $sectionLines = $bundle['lines'];
            if (empty($sectionLines)) {
                continue;
            }

            $lines[] = '### ' . $section['title'];
            if (!empty($section['description'])) {
                $lines[] = trim((string) $section['description']);
            }

            foreach ($sectionLines as $line) {
                $lines[] = trim((string) $line['content']);
            }

            $lines[] = '';
        }

        return trim(implode(PHP_EOL, $lines));
    }
}
