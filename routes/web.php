<?php

use App\Http\Controllers\PromptLineController;
use App\Http\Controllers\PromptSectionController;
use App\Http\Controllers\PromptVersionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('prompt-versions.index'));

Route::resource('prompt-versions', PromptVersionController::class);
Route::post('prompt-versions/{prompt_version}/duplicate', [PromptVersionController::class, 'duplicate'])
    ->name('prompt-versions.duplicate');

Route::post('prompt-versions/{prompt_version}/lines/reorder', [PromptLineController::class, 'reorder'])
    ->name('prompt-lines.reorder');

Route::resource('prompt-lines', PromptLineController::class)->except(['index', 'create', 'show']);

Route::resource('prompt-sections', PromptSectionController::class)->except(['show']);
Route::post('prompt-sections/reorder', [PromptSectionController::class, 'reorder'])
    ->name('prompt-sections.reorder');
