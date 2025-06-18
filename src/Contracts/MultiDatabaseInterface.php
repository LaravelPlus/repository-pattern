<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern\Contracts;

interface MultiDatabaseInterface
{
    public function runOnConnection(string $connection, callable $callback);

    public function crossConnectionQuery(
        string $connA, string $tableA,
        string $connB, string $tableB,
        string $keyA, string $keyB
    );
}
