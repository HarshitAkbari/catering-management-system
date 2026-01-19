<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Traits\ParsesDateRanges;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

abstract class BaseRepository
{
    use ParsesDateRanges;

    protected Model $model;

    // Optional: columns for 'search'
    protected array $searchable = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function getAllActive(): Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    public function find(int $id, ?array $relations = null): ?Model
    {
        if ($relations) {
            return $this->model->with($relations)->find($id);
        }

        return $this->model->find($id);
    }

    /**
     * get the collection by ids.
     *
     * @param  array<int>  $ids
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function getByIds(array $ids, ?array $relations = null): Collection
    {
        if ($relations) {
            return $this->model->with($relations)->whereIn('id', $ids)->get();
        }

        return $this->model->whereIn('id', $ids)->get();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function createMany(array $data): bool
    {
        return $this->model->insert($data);
    }

    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function forceDelete(Model $model): bool
    {
        return $model->forceDelete();
    }

    public function deleteByIds(array $ids, ?bool $force = false): int|bool
    {
        if ($force) {
            return $this->model->forceDestroy($ids);
        }

        return $this->model->destroy($ids);
    }

    public function toggleActivation(Model $model): bool
    {
        return $model->update(['is_active' => ! $model->is_active]);
    }

    /**
     * Update multiple records by their IDs
     *
     * @param  array  $filters  Array of filters to match records (e.g., ['id' => [1, 2, 3]])
     * @param  array  $data  Array of data to update
     * @return int Number of records updated
     */
    public function updateMany(array $filters, array $data): int
    {
        $query = $this->model->newQuery();

        // Apply filters using existing filter logic
        $query = $this->applyFilters($query, $filters);

        return $query->update($data);
    }

    /**
     * Apply filters to the query builder
     */
    public function applyFilters(Builder $query, array $filters): Builder
    {
        $filters = $this->filterNestedValues($filters);

        // Handle OR WHERE conditions first
        if (isset($filters['_or_where'])) {
            $this->applyOrWhereConditions($query, $filters['_or_where']);
            unset($filters['_or_where']);
        }

        foreach ($filters as $key => $value) {
            if (in_array($key, ['search', 'sort_by', 'sort_order'])) {
                continue;
            }

            if (str_ends_with($key, '_between') && is_array($value) && count($value) === 2) {
                $column = str_replace('_between', '', $key);

                // Convert date format and ensure we include the entire day range
                // Use startOfDay for from date and endOfDay for to date
                // This works for both DATE and DATETIME columns
                $from = $this->parseDateWithStartOfDay($value['from']);
                $to = $this->parseDateWithEndOfDay($value['to']);

                $query->whereBetween($column, [$from, $to]);

                continue;
            }

            if (is_array($value) && $this->isAssoc($value)) {
                // Relationship filters
                $query->whereHas($key, function ($q) use ($value) {
                    $this->applyFilters($q, $value);
                });

                continue;
            }

            if (is_array($value)) {
                // Check if array contains 'null' value
                if (in_array('null', $value)) {
                    // Remove 'null' from array but keep all other values
                    $filteredValues = array_filter($value, function ($v) {
                        return $v !== 'null';
                    });

                    if (! empty($filteredValues)) {
                        // Use OR condition: whereIn OR whereNull
                        $query->where(function ($q) use ($key, $filteredValues) {
                            $q->whereIn($key, $filteredValues)
                                ->orWhereNull($key);
                        });
                    } else {
                        // Only null values selected
                        $query->whereNull($key);
                    }
                } else {
                    $query->whereIn($key, $value);
                }
            } elseif ($value === 'null') {
                $query->whereNull($key);
            } else {
                if (str_ends_with($key, '_date')) {
                    $value = Carbon::parse($value)->toDateString();
                }

                if (str_ends_with($key, '_like')) {
                    $column = str_replace('_like', '', $key);
                    $query->where($column, 'like', "%{$value}%");
                } elseif (str_ends_with($key, '_lte')) {
                    $column = str_replace('_lte', '', $key);
                    $query->where($column, '<=', $value);
                } elseif (str_ends_with($key, '_gte')) {
                    $column = str_replace('_gte', '', $key);
                    $query->where($column, '>=', $value);
                } else {
                    $query->where($key, $value);
                }
            }
        }

        // Search support
        if (! empty($filters['search']) && ! empty($this->searchable)) {
            $query->where(function ($q) use ($filters) {
                foreach ($this->searchable as $column) {
                    $q->orWhere($column, 'like', "%{$filters['search']}%");
                }
            });
        }

        // Sorting support
        if (! empty($filters['sort_by'])) {
            $query = $this->applySmartSort($query, $filters['sort_by'], $filters['sort_order'] ?? 'asc');
        } else {
            $query->orderBy($this->model->getTable().'.'.'created_at', 'desc'); // default sort
        }

        return $query;
    }

    /**
     * Summary of applyPagination
     */
    public function applyPagination(Builder $query, array $filters = [], ?int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('app.pagination.per_page', 10);
        return $query->paginate($perPage)
            ->appends(array_merge($filters, ['has_search' => true]));
    }

    public function filterAndPaginate(array $filters = [], array $relations = [], array $withCount = [], ?int $perPage = null): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (! empty($relations)) {
            $query->with($relations);
        }

        if (! empty($withCount)) {
            $query->withCount($withCount);
        }

        if (Schema::hasColumn($this->model->getTable(), 'deleted_at')) {
            $query->whereNull($this->model->getTable().'.deleted_at');
        }

        $query = $this->applyFilters($query, $filters);

        return $this->applyPagination($query, $filters, $perPage);
    }

    /**
     * Filter the query by the filters.
     */
    public function filter(array $filters = [], array $relations = [], array $withCount = [], ?bool $returnQuery = false): Collection|Builder
    {
        $query = $this->model->newQuery();

        if (! empty($relations)) {
            $query->with($relations);
        }

        if (! empty($withCount)) {
            $query->withCount($withCount);
        }

        if (Schema::hasColumn($this->model->getTable(), 'deleted_at')) {
            $query->whereNull($this->model->getTable().'.deleted_at');
        }

        if ($returnQuery) {
            return $this->applyFilters($query, $filters);
        }

        return $this->applyFilters($query, $filters)->get();
    }

    /**
     * Summary of isAssoc
     */
    protected function isAssoc(array $array): bool
    {
        return Arr::isAssoc($array);
    }

    /**
     * Apply smart sort to the query.
     */
    protected function applySmartSort(Builder $query, string $sortBy, string $sortOrder = 'asc'): Builder
    {
        if (! str_contains($sortBy, '.')) {
            return $query->orderBy($sortBy, $sortOrder);
        }

        $segments = explode('.', $sortBy);
        $column = array_pop($segments); // last part is column
        $currentModel = $this->model;
        $relationChain = [];
        $baseKeyColumn = $this->model->getTable().'.'.$this->model->getKeyName();

        foreach ($segments as $relationName) {
            if (! method_exists($currentModel, $relationName)) {
                return $query; // invalid relationship
            }

            $relation = $currentModel->$relationName();
            $relationChain[] = $relation;

            $currentModel = $relation->getRelated();
        }

        // Build nested subquery
        $subQuery = $currentModel->newQuery();
        $previousAlias = $currentModel->getTable();

        for ($i = count($relationChain) - 1; $i >= 0; $i--) {
            $relation = $relationChain[$i];
            $relatedModel = $relation->getRelated();
            $relatedTable = $relatedModel->getTable();

            $foreignKey = $relation->getForeignKeyName();
            $ownerKey = method_exists($relation, 'getOwnerKeyName')
                ? $relation->getOwnerKeyName()
                : $relatedModel->getKeyName();

            $subQuery->whereColumn("{$relatedTable}.{$ownerKey}", $relation->getQualifiedForeignKeyName());
            $subQuery->select($column)->limit(1);
        }

        return $query->orderBy($subQuery, $sortOrder);
    }

    /**
     * Recursively filter out null, blank, or empty array values from nested arrays
     * Note: The string 'null' is preserved as it's used as a special marker for null filtering
     */
    protected function filterNestedValues(array $array): array
    {
        $filtered = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                // Recursively filter nested arrays
                $filteredValue = $this->filterNestedValues($value);

                // Only include if the filtered array is not empty
                if (! empty($filteredValue)) {
                    $filtered[$key] = $filteredValue;
                }
            } else {
                // For non-array values, check if they should be included
                if ($value !== null && $value !== '' && $value !== []) {
                    $filtered[$key] = $value;
                }
            }
        }

        return $filtered;
    }

    /**
     * Apply OR WHERE conditions to the query
     */
    protected function applyOrWhereConditions(Builder $query, array $orConditions): void
    {
        $query->where(function ($q) use ($orConditions) {
            foreach ($orConditions as $condition) {
                if (isset($condition['relation'])) {
                    // Handle relationship-based OR conditions
                    $this->applyOrWhereRelationCondition($q, $condition);
                } else {
                    // Handle direct column OR conditions
                    $this->applyOrWhereColumnCondition($q, $condition);
                }
            }
        });
    }

    /**
     * Apply OR WHERE condition for relationships
     */
    protected function applyOrWhereRelationCondition(Builder $query, array $condition): void
    {
        $relation = $condition['relation'];
        $filters = $condition['filters'] ?? [];
        $searchTerm = $condition['search_term'] ?? null;

        if ($searchTerm) {
            $query->orWhereHas($relation, function ($relationQuery) use ($filters, $searchTerm) {
                $relationQuery->where(function ($q) use ($filters, $searchTerm) {
                    foreach ($filters as $column) {
                        $q->orWhere($column, 'like', "%{$searchTerm}%");
                    }
                });
            });
        } else {
            $query->orWhereHas($relation, function ($relationQuery) use ($filters) {
                $this->applyFilters($relationQuery, $filters);
            });
        }
    }

    /**
     * Apply OR WHERE condition for direct columns
     */
    protected function applyOrWhereColumnCondition(Builder $query, array $condition): void
    {
        $column = $condition['column'];
        $operator = $condition['operator'] ?? '=';
        $value = $condition['value'];

        if ($operator === 'like') {
            $query->orWhere($column, 'like', "%{$value}%");
        } elseif ($operator === 'in') {
            $query->orWhereIn($column, $value);
        } elseif ($operator === 'null') {
            $query->orWhereNull($column);
        } else {
            $query->orWhere($column, $operator, $value);
        }
    }
}

