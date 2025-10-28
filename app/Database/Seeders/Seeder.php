<?php

declare(strict_types=1);

namespace App\Database\Seeders;

use PDO;

abstract class Seeder
{
    abstract public function run(PDO $pdo): void;
}
