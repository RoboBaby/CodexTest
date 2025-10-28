<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromptSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sectionId = $this->route('prompt_section')?->id ?? $this->route('section');

        return [
            'key' => ['required', 'string', 'max:255', 'unique:prompt_section,key,' . $sectionId],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order_index' => ['required', 'integer', 'min:0'],
            'enabled' => ['required', 'boolean'],
        ];
    }
}
