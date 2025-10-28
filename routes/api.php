<?php

use App\Http\Controllers\PromptRenderController;
use Illuminate\Support\Facades\Route;

Route::get('prompt-versions/{prompt_version}/render', [PromptRenderController::class, 'show'])
    ->name('api.prompt-versions.render');
