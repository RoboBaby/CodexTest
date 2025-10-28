<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromptLineRequest;
use App\Models\PromptLine;
use App\Models\PromptSection;
use App\Models\PromptVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PromptLineController extends Controller
{
    public function store(PromptLineRequest $request): RedirectResponse
    {
        $line = PromptLine::create($request->validated());

        return redirect()->route('prompt-versions.show', $line->version_id)
            ->with('status', 'Line created.');
    }

    public function edit(PromptLine $promptLine)
    {
        $sections = PromptSection::orderBy('order_index')->get();
        $versions = PromptVersion::orderByDesc('created_at')->get();

        return view('prompt_lines.edit', [
            'line' => $promptLine,
            'sections' => $sections,
            'versions' => $versions,
        ]);
    }

    public function update(PromptLineRequest $request, PromptLine $promptLine): RedirectResponse
    {
        $promptLine->update($request->validated());

        return redirect()->route('prompt-versions.show', $promptLine->version_id)
            ->with('status', 'Line updated.');
    }

    public function destroy(PromptLine $promptLine): RedirectResponse
    {
        $versionId = $promptLine->version_id;
        $promptLine->delete();

        return redirect()->route('prompt-versions.show', $versionId)
            ->with('status', 'Line removed.');
    }

    public function reorder(Request $request, PromptVersion $promptVersion): RedirectResponse
    {
        $data = $request->validate([
            'section_id' => ['nullable', 'integer', 'exists:prompt_section,id'],
            'order' => ['required', 'string'],
        ]);

        $ids = array_values(array_filter(array_map(function ($id) {
            return (int) trim($id);
        }, explode(',', $data['order'])), fn ($id) => $id > 0));

        $linesQuery = $promptVersion->lines();

        if (!empty($data['section_id'])) {
            $linesQuery->where('section_id', $data['section_id']);
        }

        $lines = $linesQuery->whereIn('id', $ids)->get();

        foreach ($ids as $index => $lineId) {
            $line = $lines->firstWhere('id', $lineId);
            if ($line) {
                $line->update(['order_index' => $index + 1]);
            }
        }

        return redirect()->route('prompt-versions.show', $promptVersion)
            ->with('status', 'Lines reordered.');
    }
}
