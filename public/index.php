<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap/autoload.php';

use App\Controllers\PromptLineController;
use App\Controllers\PromptSectionController;
use App\Controllers\PromptVersionController;
use App\Http\Request;
use App\Routing\Router;
use App\Repositories\PromptLineRepository;
use App\Repositories\PromptSectionRepository;
use App\Repositories\PromptVersionRepository;
use App\Services\PromptRenderer;

$request = Request::capture();
$router = new Router();

$versionRepository = new PromptVersionRepository();
$sectionRepository = new PromptSectionRepository();
$lineRepository = new PromptLineRepository();
$renderer = new PromptRenderer($versionRepository, $sectionRepository, $lineRepository);

$promptVersionController = new PromptVersionController($versionRepository, $sectionRepository, $lineRepository, $renderer);
$promptSectionController = new PromptSectionController($sectionRepository);
$promptLineController = new PromptLineController($versionRepository, $sectionRepository, $lineRepository);

require BASE_PATH . '/routes/web.php';

$response = $router->dispatch($request);
$response->send();
