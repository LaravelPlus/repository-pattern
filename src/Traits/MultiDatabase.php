<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern\Traits;

use Illuminate\Support\Facades\DB;

trait MultiDatabase
{
    public function runOnConnection(string $connection, callable $callback)
    {
        $db = DB::connection($connection);

        return $callback($db);
    }

    public function crossConnectionQuery(
        string $connA, string $tableA,
        string $connB, string $tableB,
        string $keyA, string $keyB
    ) {
        $rowsA = DB::connection($connA)->table($tableA)->get();
        $rowsB = DB::connection($connB)->table($tableB)->get();
        $groupedB = collect($rowsB)->groupBy($keyB);

        return collect($rowsA)->map(function ($rowA) use ($groupedB, $keyA) {
            $rowA->related = $groupedB[$rowA->$keyA] ?? collect();

            return $rowA;
        });
    }
}
