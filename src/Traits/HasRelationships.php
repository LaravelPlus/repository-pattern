<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern\Traits;

trait HasRelationships
{
    public function withRelations(array $relations)
    {
        return $this->model->with($relations)->get();
    }
}
