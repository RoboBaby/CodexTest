<?php

namespace App\Http\Controllers;

use App\Models\PromptVersion;
use App\Services\PromptRenderer;
use Illuminate\Http\JsonResponse;

class PromptRenderController extends Controller
{
    public function show(PromptVersion $promptVersion, PromptRenderer $renderer): JsonResponse
    {
        return response()->json([
            'prompt_name' => $promptVersion->prompt_name,
            'version_label' => $promptVersion->version_label,
            'status' => $promptVersion->status,
            'rendered_prompt' => $renderer->render($promptVersion),
        ]);
    }
}
