<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/4 0004
 * Time: 11:34
 */

namespace ddd\Quota\Domain\LogisticsQuota;


use app\ddd\Common\Repository\OrderCondition;
use app\ddd\Common\Repository\SearchCondition;
use ddd\Quota\Domain\IQuotaRepository;

interface ILogisticsDailyQuotaRepository extends IQuotaRepository
{
    /**
     * @param int $logistics_id
     * @param string $date
     * @return LogisticsDailyQuota
     */
    public function findByLogisticsId($logistics_id, $date = '');

    /**
     * @param SearchCondition[] $searchParams
     * @param OrderCondition[] $orders
     * @return mixed
     */
    public function getList($searchParams,$orders);
}