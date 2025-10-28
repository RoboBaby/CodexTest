<?php

namespace App\Services;

use App\Models\PromptVersion;

class PromptRenderer
{
    public function render(PromptVersion $version, bool $onlyEnabled = true): string
    {
        $linesQuery = $version->lines()->with(['section']);

        if ($onlyEnabled) {
            $linesQuery->where('enabled', true)
                ->whereHas('section', function ($query) {
                    $query->where('enabled', true);
                });
        }

        $lines = $linesQuery
            ->orderBy('section_id')
            ->orderBy('order_index')
            ->get()
            ->groupBy('section.key');

        $rendered = [];

        foreach ($lines as $sectionKey => $sectionLines) {
            $sectionTitle = optional($sectionLines->first()->section)->title ?? $sectionKey;
            $rendered[] = "### {$sectionTitle}";

            foreach ($sectionLines as $line) {
                $rendered[] = $line->content;
            }
        }

        return implode("\n", $rendered);
    }
}
