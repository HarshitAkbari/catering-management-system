<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseService
{
    protected readonly BaseRepository $baseRepository;

    public function __construct(BaseRepository $baseRepository)
    {
        $this->baseRepository = $baseRepository;
    }

    public function getAll(): Collection
    {
        return $this->baseRepository->all();
    }

    public function getAllActive(): Collection
    {
        return $this->baseRepository->getAllActive();
    }

    public function getById(int $id, ?array $relations = null): ?Model
    {
        return $this->baseRepository->find($id, $relations);
    }

    /**
     * get the collection by ids.
     *
     * @param  array<int>  $ids
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function getByIds(array $ids, ?array $relations = null): Collection
    {
        return $this->baseRepository->getByIds($ids, $relations);
    }

    public function create(array $data): Model|array|null
    {
        return $this->baseRepository->create($data);
    }

    public function createMany(array $data): bool
    {
        return $this->baseRepository->createMany($data);
    }

    public function update(Model $model, array $data): bool|array
    {
        return $this->baseRepository->update($model, $data);
    }

    public function delete(Model $model): bool
    {
        return $this->baseRepository->delete($model);
    }

    public function forceDelete(Model $model): bool
    {
        return $this->baseRepository->forceDelete($model);
    }

    public function deleteByIds(array $ids, ?bool $force = false): int|bool
    {
        return $this->baseRepository->deleteByIds($ids, $force);
    }

    public function toggleActivation(Model $model): bool
    {
        return $this->baseRepository->toggleActivation($model);
    }

    /**
     * Update multiple records by filters
     *
     * @param  array  $filters  Array of filters to match records (e.g., ['id' => [1, 2, 3]])
     * @param  array  $data  Array of data to update
     * @return int Number of records updated
     */
    public function updateMany(array $filters, array $data): int
    {
        return $this->baseRepository->updateMany($filters, $data);
    }
}

