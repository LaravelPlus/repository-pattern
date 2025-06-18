<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int|string $id): ?Model;

    public function findBy(string $field, mixed $value): ?Model;

    public function create(array $data): Model;

    public function update(int|string $id, array $data): ?Model;

    public function delete(int|string $id): bool;
}
