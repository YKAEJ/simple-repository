<?php

namespace Ykaej\Repository\Criteria;

use Ykaej\Repository\Contracts\RepositoryInterface;

/**
 * Class Criteria
 * @package Ykaej\Repository\Criteria
 */
abstract class Criteria
{
    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public abstract function apply($model, RepositoryInterface $repository);
}