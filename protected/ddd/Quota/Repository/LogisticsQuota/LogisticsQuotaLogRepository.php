<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 16:09
 */

namespace ddd\Quota\Repository\LogisticsQuota;


use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\Quota\Domain\LogisticsQuota\ILogisticsQuotaLogRepository;
use ddd\Quota\Domain\LogisticsQuota\LogisticsQuotaLog;

class LogisticsQuotaLogRepository extends EntityRepository implements ILogisticsQuotaLogRepository
{
    public function getNewEntity()
    {
        return new LogisticsQuotaLog();
    }

    public function getActiveRecordClassName()
    {
        return 'LogisticsQuotaLog';
    }

    /**
     * 获取额度变更记录对象id
     * @return mixed
     */
    public function getQuotaObjectId(IAggregateRoot $entity)
    {
        return $entity->logistics_id;
    }
}