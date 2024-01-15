<?php

namespace App\Repositories\Eloquent;

use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BaseRepository implements EloquentRepositoryInterface
{
    protected Model $model;

    /**
     * BaseRepository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function find(int $id, array $with = [], array $params = []): ?Model
    {
        return $this->model->with($with)->findOrFail($id);
    }

    public function findWithTrash(int $id): ?Model
    {
        return $this->model::query()->withTrashed()->find($id);
    }

    public function delete(int $id)
    {
        return $this->model::query()->find($id)->delete();
    }

    public function update(int $id, array $attributes): bool
    {
        $model = $this->find($id);

        return $model->update($attributes);
    }

    public function where(string $column, mixed $operator = '=', mixed $value =null ,array $with = []): ?Model
    {
        return $this->model->with($with)->where($column,$operator,$value)->first();
    }
}
