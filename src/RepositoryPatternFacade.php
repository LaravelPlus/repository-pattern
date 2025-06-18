<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern;

use Illuminate\Support\Facades\Facade;

/**
 * @see Skeleton\SkeletonClass
 */
final class RepositoryPatternFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'repository-pattern';
    }
}
