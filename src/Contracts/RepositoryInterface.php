<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @template TModel of Model
 */
interface RepositoryInterface
{
    /**
     * Get all records.
     *
     * @return Collection<int, TModel>
     */
    public function all(): Collection;

    /**
     * Paginate records.
     *
     * @return LengthAwarePaginator<int, TModel>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a record by ID.
     *
     * @return TModel|null
     */
    public function find(int|string $id): ?Model;

    /**
     * Find a record by field value.
     *
     * @return TModel|null
     */
    public function findBy(string $field, mixed $value): ?Model;

    /**
     * Create a new record.
     *
     * @param  array<string, mixed>  $data
     * @return TModel
     */
    public function create(array $data): Model;

    /**
     * Update a record by ID.
     *
     * @param  array<string, mixed>  $data
     * @return TModel|null
     */
    public function update(int|string $id, array $data): ?Model;

    /**
     * Delete a record by ID.
     */
    public function delete(int|string $id): bool;
}
