<?php

declare(strict_types=1);

require BASE_PATH . '/bootstrap/autoload.php';

use App\Database\Connection;
use App\Database\Migrations\Migrator;
use App\Database\Seeders\DatabaseSeeder;
use App\Repositories\PromptLineRepository;
use App\Repositories\PromptSectionRepository;
use App\Repositories\PromptVersionRepository;
use App\Services\PromptRenderer;

Connection::override(['driver' => 'sqlite', 'database' => ':memory:']);

$migrator = new Migrator(BASE_PATH . '/database/migrations');
$migrator->migrate();
$seeder = new DatabaseSeeder();
$seeder->run();

$versions = new PromptVersionRepository();
$sections = new PromptSectionRepository();
$lines = new PromptLineRepository();
$renderer = new PromptRenderer($versions, $sections, $lines);

$identity = $sections->findByKey('identity');
$style = $sections->findByKey('style_tone');
assertNotNull($identity, 'Identity section should exist after seeding.');
assertNotNull($style, 'Style section should exist after seeding.');

$versionId = $versions->create([
    'prompt_name' => 'AskGVT',
    'version_label' => 'v1-test',
    'status' => 'draft',
    'notes' => 'Test version',
]);

$lines->create([
    'version_id' => $versionId,
    'section_id' => $identity['id'],
    'content' => 'You are AskGVT, a helpful AI.',
    'enabled' => 1,
]);
$lines->create([
    'version_id' => $versionId,
    'section_id' => $style['id'],
    'content' => 'Use a friendly, professional tone.',
    'enabled' => 1,
]);

$output = $renderer->renderText($versionId);
assertTrue(str_contains($output, '### Identity'), 'Rendered prompt should include section headings.');
assertTrue(str_contains($output, 'friendly, professional tone'), 'Rendered prompt should include line content.');

$allVersions = $versions->all();
assertTrue(count($allVersions) === 1, 'Exactly one version should exist.');

$versions->setStatus($versionId, 'active');
$updated = $versions->find($versionId);
assertTrue($updated['status'] === 'active', 'Status should update to active.');

$linesList = $lines->forVersion($versionId, null, true);
assertTrue(count($linesList) === 2, 'Version should have two lines.');

$lines->move($linesList[1]['id'], -1);
$movedLine = $lines->find($linesList[1]['id']);
assertTrue((int) $movedLine['order_index'] === 1, 'Move should update ordering.');
$linesAfterMove = $lines->forVersion($versionId, null, true);

$lines->delete($linesAfterMove[0]['id']);
$lines->resequence($versionId);
assertTrue(count($lines->forVersion($versionId, null, true)) === 1, 'Deleting a line should reduce count.');

echo "All tests passed" . PHP_EOL;

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException('Assertion failed: ' . $message);
    }
}

function assertNotNull(mixed $value, string $message): void
{
    if ($value === null) {
        throw new RuntimeException('Assertion failed: ' . $message);
    }
}
