<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromptSectionRequest;
use App\Models\PromptSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromptSectionController extends Controller
{
    public function index(Request $request): View
    {
        $sections = PromptSection::query()
            ->when($request->boolean('enabled'), fn ($query) => $query->where('enabled', true))
            ->orderBy('order_index')
            ->get();

        return view('prompt_sections.index', compact('sections'));
    }

    public function create(): View
    {
        return view('prompt_sections.create');
    }

    public function store(PromptSectionRequest $request): RedirectResponse
    {
        $section = PromptSection::create($request->validated());

        return redirect()->route('prompt-sections.index')
            ->with('status', "Section {$section->key} created.");
    }

    public function edit(PromptSection $promptSection): View
    {
        return view('prompt_sections.edit', ['section' => $promptSection]);
    }

    public function update(PromptSectionRequest $request, PromptSection $promptSection): RedirectResponse
    {
        $promptSection->update($request->validated());

        return redirect()->route('prompt-sections.index')
            ->with('status', 'Section updated.');
    }

    public function destroy(PromptSection $promptSection): RedirectResponse
    {
        $promptSection->delete();

        return redirect()->route('prompt-sections.index')
            ->with('status', 'Section removed.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order' => ['required', 'string'],
        ]);

        $ids = array_values(array_filter(array_map(function ($id) {
            return (int) trim($id);
        }, explode(',', $data['order'])), fn ($id) => $id > 0));

        foreach ($ids as $index => $sectionId) {
            PromptSection::whereKey($sectionId)->update(['order_index' => $index + 1]);
        }

        return redirect()->route('prompt-sections.index')
            ->with('status', 'Sections reordered.');
    }
}
