<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern\Traits;

use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    public function cacheAll(int $minutes = 10)
    {
        $key = static::class . '_all';
        return Cache::remember($key, $minutes * 60, fn() => $this->all());
    }
} 