<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern\Traits;

trait Eventable
{
    protected function fireEvent(string $event, array $payload = []): void
    {
        event($event, $payload);
    }
} 