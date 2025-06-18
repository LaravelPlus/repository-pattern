<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern;

use Illuminate\Support\Facades\DB;

final class RepositoryService
{
    public static function map($results, callable $callback)
    {
        return collect($results)->map($callback);
    }

    public static function mapWithKeys($results, callable $callback)
    {
        return collect($results)->mapWithKeys($callback);
    }

    public static function modifyFields($results, array $modifiers)
    {
        return collect($results)->map(function ($item) use ($modifiers) {
            foreach ($modifiers as $field => $callback) {
                if (is_array($item) && array_key_exists($field, $item)) {
                    $item[$field] = $callback($item[$field], $item);
                } elseif (is_object($item) && property_exists($item, $field)) {
                    $item->$field = $callback($item->$field, $item);
                }
            }

            return $item;
        });
    }

    public static function runOnConnection(string $connection, callable $callback)
    {
        $db = DB::connection($connection);

        return $callback($db);
    }

    public static function crossConnectionQuery(
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
