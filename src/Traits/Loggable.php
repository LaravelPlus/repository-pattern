<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern\Traits;

use Illuminate\Support\Facades\Log;

trait Loggable
{
    public function logAction(string $action, array $data = []): void
    {
        Log::info(static::class . ' action: ' . $action, $data);
    }
}
