<?php

namespace Ykaej\Repository\Contracts;

/**
 * Interface RepositoryInterface
 * @package Ykaej\Repository\Contracts
 */
interface RepositoryInterface
{
    /**
     * @param array $columns
     * @return mixed
     */
    public function all($columns = ['*']);

    /**
     * @param int $limit
     * @param array $columns
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*']);

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = ['*']);

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function findOrFail($id, $columns = ['*']);

    /**
     * @param array $columns
     * @return mixed
     */
    public function first($columns = ['*']);

    /**
     * @param $field
     * @param null $value
     * @param array $columns
     * @return mixed
     */
    public function findByField($field, $value = null, $columns = ['*']);

    /**
     * @param array $where
     * @param array $columns
     * @return mixed
     */
    public function findWhere(array $where, $columns = ['*']);

    /**
     * @param $field
     * @param array $values
     * @param array $columns
     * @return mixed
     */
    public function findWhereIn($field, array $values, $columns = ['*']);

    /**
     * @param $field
     * @param array $values
     * @param array $columns
     * @return mixed
     */
    public function findWhereNotIn($field, array $values, $columns = ['*']);

    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * @param array $attributes
     * @param $id
     * @return mixed
     */
    public function update(array $attributes, $id);

    /**
     * @param array $multipleData
     * @return mixed
     */
    public function updateBatch(array $multipleData);

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * @return mixed
     */
    public function forceDelete();

    /**
     * @param array $where
     * @return mixed
     */
    public function deleteWhere(array $where);

    /**
     * @param $field
     * @param array $values
     * @return mixed
     */
    public function deleteWhereIn($field, array $values);

    /**
     * @param string $name
     * @param int $amount
     * @return mixed
     */
    public function increment(string $name, int $amount = 1);

    /**
     * @param string $name
     * @param int $amount
     * @return mixed
     */
    public function decrement(string $name, int $amount = 1);

    /**
     * @param $relation
     * @return mixed
     */
    public function has($relation);

    /**
     * @param $relations
     * @return mixed
     */
    public function with($relations);

    /**
     * @param $relations
     * @return mixed
     */
    public function withCount($relations);

    /**
     * @param $relation
     * @param $closure
     * @return mixed
     */
    public function whereHas($relation, $closure);

    /**
     * @param $column
     * @param string $direction
     * @return mixed
     */
    public function orderBy($column, $direction = 'asc');

    /**
     * @param $field
     * @param $condition
     * @param null $value
     * @param string $boolean
     * @return mixed
     */
    public function where($field, $condition, $value = null, $boolean = 'and');

    /**
     * @param $field
     * @param array $values
     * @return mixed
     */
    public function whereIn($field, array $values);
}