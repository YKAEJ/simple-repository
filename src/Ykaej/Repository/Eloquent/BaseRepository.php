<?php

namespace Ykaej\Repository\Eloquent;

use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Ykaej\Repository\Contracts\CriteriaInterface;
use Ykaej\Repository\Contracts\RepositoryInterface;
use Ykaej\Repository\Criteria\Criteria;
use Ykaej\Repository\Exceptions\RepositoryException;

/**
 * Class BaseRepository
 * @package Ykaej\Repository\Eloquent
 */
abstract class BaseRepository implements RepositoryInterface, CriteriaInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Model
     */
    protected $model;
    /**
     * @var Collection
     */
    protected $criteria;
    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * BaseRepository constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->criteria = new Collection();
        $this->makeModel();
        $this->boot();
    }

    /**
     *
     */
    public function boot()
    {
    }

    /**
     * @return mixed
     */
    abstract public function model();

    /**
     * @return Model|mixed
     * @throws RepositoryException
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());
        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }
        return $this->model = $model;
    }

    /**
     * @throws RepositoryException
     */
    public function resetModel()
    {
        $this->makeModel();
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        $this->applyCriteria();

        $results = $this->model->get($columns);

        $this->resetModel();
        return $results;
    }

    /**
     * @param null $limit
     * @param array $columns
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'])
    {
        $this->applyCriteria();

        $limit = is_null($limit) ? config('repository.paginate.limit', 15) : $limit;
        $results = $this->model->paginate($limit, $columns);

        $this->resetModel();
        return $results;
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        $this->applyCriteria();

        $model = $this->model->find($id, $columns);

        $this->resetModel();
        return $model;
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function findOrFail($id, $columns = ['*'])
    {
        $this->applyCriteria();

        $model = $this->model->findOrFail($id, $columns);

        $this->resetModel();
        return $model;
    }

    /**
     * @param $field
     * @param null $value
     * @param array $columns
     * @return mixed
     */
    public function findByField($field, $value = null, $columns = ['*'])
    {
        $this->applyCriteria();

        $model = $this->model->where($field, '=', $value)->get($columns);

        $this->resetModel();
        return $model;
    }

    /**
     * @param array $where
     * @param array $columns
     * @return mixed
     */
    public function findWhere(array $where, $columns = ['*'])
    {
        $this->applyCriteria();

        $this->applyConditions($where);
        $model = $this->model->get($columns);

        $this->resetModel();
        return $model;
    }

    /**
     * @param $field
     * @param array $values
     * @param array $columns
     * @return mixed
     */
    public function findWhereIn($field, array $values, $columns = ['*'])
    {
        $this->applyCriteria();

        $model = $this->model->whereIn($field, $values)->get($columns);

        $this->resetModel();
        return $model;
    }

    /**
     * @param $field
     * @param array $values
     * @param array $columns
     * @return mixed
     */
    public function findWhereNotIn($field, array $values, $columns = ['*'])
    {
        $this->applyCriteria();

        $model = $this->model->whereNotIn($field, $values)->get($columns);

        $this->resetModel();
        return $model;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * @param array $attributes
     * @param $id
     * @return mixed
     */
    public function update(array $attributes, $id)
    {
        $model = $this->findOrFail($id);
        $model->fill($attributes);
        $model->save();

        return $model;
    }

    /**
     *
     * 批量更新
     * ['id' => 1, 'name' => '张三', 'email' => 'zhansan@qq.com'],
     * ['id' => 2, 'name' => '李四', 'email' => 'lisi@qq.com'],
     * @param array $multipleData
     * @return bool|int
     */
    public function updateBatch(array $multipleData)
    {
        try {
            if (empty($multipleData)) {
                throw new RepositoryException("数据不能为空");
            }
            $tableName = \DB::getTablePrefix() . $this->model->getTable(); // 表名
            $firstRow = current($multipleData);

            $updateColumn = array_keys($firstRow);
            // 默认以id为条件更新，如果没有ID则以第一个字段为条件
            $referenceColumn = isset($firstRow['id']) ? 'id' : current($updateColumn);//code
            unset($updateColumn[0]);
            // 拼接sql语句
            $updateSql = "UPDATE " . $tableName . " SET ";
            $sets = [];
            $bindings = [];
            foreach ($updateColumn as $uColumn) {
                $setSql = "`" . $uColumn . "` = CASE ";
                foreach ($multipleData as $data) {
                    $setSql .= "WHEN `" . $referenceColumn . "` = ? THEN ? ";
                    $bindings[] = $data[$referenceColumn];
                    $bindings[] = $data[$uColumn]; //value
                }
                $setSql .= "ELSE `" . $uColumn . "` END ";
                $sets[] = $setSql;
            }
            $updateSql .= implode(', ', $sets);

            $whereIn = collect($multipleData)->pluck($referenceColumn)->values()->all();
            $bindings = array_merge($bindings, $whereIn);
            $whereIn = rtrim(str_repeat('?,', count($whereIn)), ',');
            $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ")";
            // 传入预处理sql语句和对应绑定数据
            return \DB::update($updateSql, $bindings);
        } catch (RepositoryException $e) {
            return false;
        }
    }

    /**
     * @param $id
     * @return int
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * @return bool|null
     */
    public function forceDelete()
    {
        $this->applyCriteria();

        $result = $this->model->forceDelete();

        $this->resetModel();
        return $result;
    }

    /**
     * @param array $where
     * @return bool|null
     */
    public function deleteWhere(array $where)
    {
        $this->applyCriteria();

        $this->applyConditions($where);
        $deleted = $this->model->delete();

        $this->resetModel();
        return $deleted;
    }

    /**
     * @param $field
     * @param array $values
     * @return mixed
     */
    public function deleteWhereIn($field, array $values)
    {
        $this->applyCriteria();

        $deleted = $this->model->whereIn($field, $values)->delete();

        $this->resetModel();
        return $deleted;
    }

    /**
     * @param string $name
     * @param int $amount
     * @return int
     */
    public function increment(string $name, int $amount = 1)
    {
        $this->applyCriteria();

        $result = $this->model->increment($name, $amount);

        $this->resetModel();
        return $result;
    }

    /**
     * @param string $name
     * @param int $amount
     * @return int
     */
    public function decrement(string $name, int $amount = 1)
    {
        $this->applyCriteria();

        $result = $this->model->decrement($name, $amount);

        $this->resetModel();
        return $result;
    }

    /**
     * @param $relation
     * @return $this
     */
    public function has($relation)
    {
        $this->model = $this->model->has($relation);

        return $this;
    }

    /**
     * @param $relations
     * @return $this
     */
    public function with($relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * @param $relations
     * @return $this
     */
    public function withCount($relations)
    {
        $this->model = $this->model->withCount($relations);

        return $this;
    }

    /**
     * @param $relation
     * @param $closure
     * @return $this
     */
    public function whereHas($relation, $closure)
    {
        $this->model = $this->model->whereHas($relation, $closure);

        return $this;
    }

    /**
     * @param $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->model = $this->model->orderBy($column, $direction);

        return $this;
    }

    /**
     * @param $field
     * @param $condition
     * @param null $value
     * @param string $boolean
     * @return $this
     */
    public function where($field, $condition, $value = null, $boolean = 'and')
    {
        $this->model = $this->model->where($field, $condition, $value, $boolean);

        return $this;
    }

    /**
     * @param $field
     * @param array $values
     * @return $this
     */
    public function whereIn($field, array $values)
    {
        $this->model = $this->model->whereIn($field, $values);

        return $this;
    }

    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function pushCriteria(Criteria $criteria)
    {
        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function popCriteria(Criteria $criteria)
    {
        $this->criteria->reject(function ($item) use ($criteria) {
            return get_class($item) === get_class($criteria);
        });

        return $this;
    }

    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function getByCriteria(Criteria $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * @return $this
     */
    public function resetCriteria()
    {
        $this->criteria = new Collection();

        return $this;
    }

    /**
     * @return $this
     */
    public function applyCriteria()
    {
        if ($this->skipCriteria === true) {
            return $this;
        }

        $criteria = $this->getCriteria();
        if ($criteria) {
            foreach ($criteria as $c) {
                if ($c instanceof Criteria) {
                    $this->model = $c->apply($this->model, $this);
                }
            }
        }
        return $this;
    }

    /**
     * @param array $where
     */
    public function applyConditions(array $where)
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                $this->model = $this->model->where($field, $condition, $val);
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }

}