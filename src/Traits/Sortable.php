<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern\Traits;

trait Sortable
{
    public function sortBy(string $column, string $direction = 'asc')
    {
        return $this->model->orderBy($column, $direction)->get();
    }
}
