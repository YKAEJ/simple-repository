<?php

namespace Ykaej\Repository\Contracts;

use Ykaej\Repository\Criteria\Criteria;

/**
 * Interface CriteriaInterface
 * @package Ykaej\Repository\Contracts
 */
interface CriteriaInterface
{
    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function pushCriteria(Criteria $criteria);

    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function popCriteria(Criteria $criteria);

    /**
     * @param Criteria $criteria
     * @return mixed
     */
    public function getByCriteria(Criteria $criteria);

    /**
     * @return mixed
     */
    public function getCriteria();

    /**
     * @param bool $status
     * @return $this
     */
    public function skipCriteria($status = true);

    /**
     * @return $this
     */
    public function resetCriteria();
}