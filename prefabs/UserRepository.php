<?php

declare(strict_types=1);

namespace Prefabs;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravelplus\RepositoryPattern\BaseRepository;

final class UserRepository extends BaseRepository
{
    public function create(array $data): Model
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        return parent::create($data);
    }

    /**
     * Search users by name or email (partial match).
     */
    public function search(string $query): Collection
    {
        return $this->model->newQuery()
            ->where(function ($q) use ($query): void {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->orderByDesc('id')->get();
    }
}
