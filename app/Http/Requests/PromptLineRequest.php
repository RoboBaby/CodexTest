<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromptLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'version_id' => ['required', 'exists:prompt_version,id'],
            'section_id' => ['required', 'exists:prompt_section,id'],
            'order_index' => ['required', 'integer', 'min:0'],
            'enabled' => ['required', 'boolean'],
            'content' => ['required', 'string'],
        ];
    }
}
