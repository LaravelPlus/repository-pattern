<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern\Traits;

trait Searchable
{
    public function search(string $column, string $term)
    {
        return $this->model->where($column, 'LIKE', '%' . $term . '%')->get();
    }
}
