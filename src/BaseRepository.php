<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravelplus\RepositoryPattern\Contracts\RepositoryInterface;

/**
 * @template TModel of Model
 *
 * @implements RepositoryInterface<TModel>
 */
abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection<int, TModel>
     */
    public function all(): Collection
    {
        /** @var Collection<int, TModel> $result */
        $result = $this->model->newQuery()->orderByDesc('id')->get();

        return $result;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()->orderByDesc('id')->paginate($perPage);
    }

    /**
     * @return TModel|null
     */
    public function find(int|string $id): ?Model
    {
        /** @var TModel|null $result */
        $result = $this->model->newQuery()->find($id);

        return $result;
    }

    /**
     * @return TModel|null
     */
    public function findBy(string $field, mixed $value): ?Model
    {
        /** @var TModel|null $result */
        $result = $this->model->newQuery()->where($field, $value)->first();

        return $result;
    }

    /**
     * @return TModel
     */
    public function create(array $data): Model
    {
        /** @var TModel $result */
        $result = $this->model->newQuery()->create($data);

        return $result;
    }

    public function update(int|string $id, array $data): ?Model
    {
        $record = $this->find($id);
        if (!$record) {
            return null;
        }
        $record->update($data);

        return $record;
    }

    public function delete(int|string $id): bool
    {
        $record = $this->find($id);

        return $record ? (bool) $record->delete() : false;
    }
}
