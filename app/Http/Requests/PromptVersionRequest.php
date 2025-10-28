<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\PromptVersion;

class PromptVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt_name' => ['required', 'string', 'max:255'],
            'version_label' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:' . implode(',', PromptVersion::statuses())],
            'notes' => ['nullable', 'string'],
        ];
    }
}
