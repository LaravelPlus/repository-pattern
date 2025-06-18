<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern\Traits;

use Illuminate\Support\Carbon;

trait SoftDeletes
{
    public function softDelete($id): bool
    {
        return (bool) $this->model->where($this->primaryKey, $id)->update(['deleted_at' => Carbon::now()]);
    }

    public function restore($id): bool
    {
        return (bool) $this->model->where($this->primaryKey, $id)->update(['deleted_at' => null]);
    }

    public function onlyTrashed()
    {
        return $this->model->whereNotNull('deleted_at')->get();
    }
} 