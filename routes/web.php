<?php

declare(strict_types=1);

use App\Controllers\PromptLineController;
use App\Controllers\PromptSectionController;
use App\Controllers\PromptVersionController;
use App\Http\RedirectResponse;
use App\Http\Request;
use App\Routing\Router;

/** @var Router $router */
/** @var PromptVersionController $promptVersionController */
/** @var PromptSectionController $promptSectionController */
/** @var PromptLineController $promptLineController */

$router->get('/', function () {
    return new RedirectResponse('/versions');
});

$router->get('/versions', fn (Request $request) => $promptVersionController->index($request));
$router->get('/versions/create', fn (Request $request) => $promptVersionController->create($request));
$router->post('/versions/create', fn (Request $request) => $promptVersionController->store($request));
$router->get('/versions/{id}', fn (Request $request, array $params) => $promptVersionController->show($request, $params));
$router->get('/versions/{id}/edit', fn (Request $request, array $params) => $promptVersionController->edit($request, $params));
$router->post('/versions/{id}/edit', fn (Request $request, array $params) => $promptVersionController->update($request, $params));
$router->post('/versions/{id}/delete', fn (Request $request, array $params) => $promptVersionController->destroy($request, $params));
$router->post('/versions/{id}/status', fn (Request $request, array $params) => $promptVersionController->updateStatus($request, $params));
$router->post('/versions/{id}/duplicate', fn (Request $request, array $params) => $promptVersionController->duplicate($request, $params));
$router->get('/api/prompts/{id}', fn (Request $request, array $params) => $promptVersionController->renderPrompt($request, $params));

$router->get('/sections', fn (Request $request) => $promptSectionController->index($request));
$router->get('/sections/create', fn (Request $request) => $promptSectionController->create($request));
$router->post('/sections/create', fn (Request $request) => $promptSectionController->store($request));
$router->get('/sections/{id}/edit', fn (Request $request, array $params) => $promptSectionController->edit($request, $params));
$router->post('/sections/{id}/edit', fn (Request $request, array $params) => $promptSectionController->update($request, $params));

$router->get('/versions/{version}/lines/create', fn (Request $request, array $params) => $promptLineController->create($request, $params));
$router->post('/versions/{version}/lines/create', fn (Request $request, array $params) => $promptLineController->store($request, $params));
$router->get('/lines/{id}/edit', fn (Request $request, array $params) => $promptLineController->edit($request, $params));
$router->post('/lines/{id}/edit', fn (Request $request, array $params) => $promptLineController->update($request, $params));
$router->post('/lines/{id}/delete', fn (Request $request, array $params) => $promptLineController->destroy($request, $params));
$router->post('/lines/{id}/move', fn (Request $request, array $params) => $promptLineController->move($request, $params));
