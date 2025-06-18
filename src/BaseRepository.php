<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Laravelplus\RepositoryPattern\Contracts\RepositoryInterface;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * The model instance.
     *
     * @var Model
     */
    protected $model;

    // Static properties for configuration
    protected static string $modelClass = '';

    protected static string $table = '';

    protected static string $connection = 'mysql';

    protected static string $primaryKey = 'id';

    protected static array $relations = [];

    protected static array $casts = [];

    protected static array $hidden = [];

    /**
     * BaseRepository constructor.
     */
    public function __construct(?Model $model = null)
    {
        $modelClass = static::$modelClass ?: Model::class;
        $this->model = $model ?? new $modelClass();
        $this->table = static::$table ?: $this->model->getTable();
        $defaultConnection = config('database.default');
        $conn = static::$connection ?: $this->model->getConnectionName();
        $this->connection = ($conn && $conn !== $defaultConnection) ? $conn : null;
    }

    protected function getQuery()
    {
        if ($this->connection) {
            return DB::connection($this->connection)->table($this->table);
        }

        return DB::table($this->table);
    }

    public function __get($name)
    {
        if ($name === 'table') {
            return $this->getQuery();
        }
        if ($name === 'model') {
            return $this->model;
        }
        if ($name === 'relations') {
            return static::$relations;
        }
        if ($name === 'casts') {
            return static::$casts;
        }

        return $this->$name;
    }

    public function __call($method, $arguments)
    {
        if (isset(static::$relations[$method])) {
            $relation = static::$relations[$method];
            $defaultConnection = config('database.default');
            if (is_string($relation) && class_exists($relation)) {
                return new $relation();
            }
            if (is_string($relation)) {
                $connection = static::$connection;
                if (!$connection || $connection === $defaultConnection) {
                    return DB::table($relation);
                }

                return DB::connection($connection)->table($relation);
            }
            if (is_array($relation) && isset($relation['table'])) {
                $connection = $relation['connection'] ?? static::$connection;
                $primaryKey = $relation['primaryKey'] ?? 'id';
                $foreignKey = $relation['foreignKey'] ?? null;
                $query = (!$connection || $connection === $defaultConnection)
                    ? DB::table($relation['table'])
                    : DB::connection($connection)->table($relation['table']);
                if ($foreignKey && isset($arguments[0])) {
                    $query->where($foreignKey, $arguments[0]);
                }

                return $query;
            }
        }
        throw new BadMethodCallException("Relation or method '{$method}' not defined in " . static::class);
    }

    protected function hideFields($result)
    {
        $hidden = static::$hidden;
        if (empty($hidden)) {
            return $result;
        }
        if ($result instanceof \Illuminate\Support\Collection || is_array($result)) {
            return collect($result)->map(function ($item) use ($hidden) {
                foreach ($hidden as $field) {
                    if (is_array($item) && array_key_exists($field, $item)) {
                        unset($item[$field]);
                    } elseif (is_object($item) && property_exists($item, $field)) {
                        unset($item->$field);
                    }
                }

                return $item;
            });
        }
        if (is_object($result) || is_array($result)) {
            foreach ($hidden as $field) {
                if (is_array($result) && array_key_exists($field, $result)) {
                    unset($result[$field]);
                } elseif (is_object($result) && property_exists($result, $field)) {
                    unset($result->$field);
                }
            }
        }

        return $result;
    }

    /**
     * Get all records.
     */
    public function all(): Collection
    {
        $results = $this->model->all();

        return $this->hideFields($results);
    }

    /**
     * Find a record by ID.
     */
    public function find(int|string $id): ?Model
    {
        $result = $this->model->where(static::$primaryKey, $id)->first();

        return $this->hideFields($result);
    }

    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record by ID.
     */
    public function update(int|string $id, array $data): ?Model
    {
        $model = $this->find($id);
        if ($model) {
            $model->update($data);

            return $model;
        }

        return null;
    }

    /**
     * Delete a record by ID.
     */
    public function delete(int|string $id): bool
    {
        return (bool) $this->model->where(static::$primaryKey, $id)->delete();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function findBy(string $field, mixed $value): ?Model
    {
        return $this->model->where($field, $value)->first();
    }

    // Utility methods are now available via RepositoryService:
    // - RepositoryService::map($results, fn($item) => ...)
    // - RepositoryService::mapWithKeys($results, fn($item) => ...)
    // - RepositoryService::modifyFields($results, ['field' => fn($v, $item) => ...])
    // - RepositoryService::runOnConnection('mysql2', fn($db) => ...)
    // - RepositoryService::crossConnectionQuery(...)
}
