<?php

namespace Laravelplus\RepositoryPattern;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Laravelplus\RepositoryPattern\Skeleton\SkeletonClass
 */
class RepositoryPatternFacade extends Facade
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
