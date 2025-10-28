<?php

declare(strict_types=1);

namespace App\Database\Seeders;

use App\Database\Connection;
use PDO;

final class DatabaseSeeder extends Seeder
{
    /** @var array<int,Seeder> */
    private array $seeders;

    public function __construct()
    {
        $this->seeders = [
            new PromptSectionSeeder(),
        ];
    }

    public function run(?PDO $pdo = null): void
    {
        $pdo ??= Connection::pdo();

        foreach ($this->seeders as $seeder) {
            $seeder->run($pdo);
        }
    }
}
