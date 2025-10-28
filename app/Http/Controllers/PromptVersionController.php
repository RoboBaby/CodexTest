<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromptVersionRequest;
use App\Models\PromptLine;
use App\Models\PromptSection;
use App\Models\PromptVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PromptVersionController extends Controller
{
    public function index(): View
    {
        $versions = PromptVersion::orderByDesc('created_at')->paginate();

        return view('prompt_versions.index', compact('versions'));
    }

    public function create(): View
    {
        $statuses = PromptVersion::statuses();

        return view('prompt_versions.create', compact('statuses'));
    }

    public function store(PromptVersionRequest $request): RedirectResponse
    {
        $version = PromptVersion::create($request->validated());

        return redirect()->route('prompt-versions.show', $version)
            ->with('status', 'Prompt version created successfully.');
    }

    public function show(PromptVersion $promptVersion): View
    {
        $promptVersion->load(['lines.section' => function ($query) {
            $query->orderBy('order_index');
        }]);

        $sections = PromptSection::orderBy('order_index')->get();

        return view('prompt_versions.show', [
            'version' => $promptVersion,
            'sections' => $sections,
        ]);
    }

    public function edit(PromptVersion $promptVersion): View
    {
        $statuses = PromptVersion::statuses();

        return view('prompt_versions.edit', [
            'version' => $promptVersion,
            'statuses' => $statuses,
        ]);
    }

    public function update(PromptVersionRequest $request, PromptVersion $promptVersion): RedirectResponse
    {
        $promptVersion->update($request->validated());

        return redirect()->route('prompt-versions.show', $promptVersion)
            ->with('status', 'Prompt version updated successfully.');
    }

    public function destroy(PromptVersion $promptVersion): RedirectResponse
    {
        $promptVersion->delete();

        return redirect()->route('prompt-versions.index')
            ->with('status', 'Prompt version deleted.');
    }

    public function duplicate(PromptVersion $promptVersion): RedirectResponse
    {
        $clone = $promptVersion->replicate(['version_label', 'status']);
        $clone->status = PromptVersion::STATUS_DRAFT;
        $clone->version_label = $clone->version_label . '-copy';
        $clone->push();

        $promptVersion->lines->each(function (PromptLine $line) use ($clone) {
            $clone->lines()->create($line->replicate(['version_id'])->toArray());
        });

        return redirect()->route('prompt-versions.edit', $clone)
            ->with('status', 'Prompt version duplicated.');
    }
}
