<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 16:42
 */

namespace ddd\Quota\Domain\LogisticsQuota;


use ddd\Common\Domain\Value\DateTime;
use ddd\Common\IAggregateRoot;

class LogisticsDailyQuotaLog extends BaseLogisticsQuotaLog implements IAggregateRoot
{
    function getIdName()
    {
        return "log_id";
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    /**
     * 创建
     * @param    LogisticsDailyQuota $logisticsDailyQuota
     * @return   LogisticsDailyQuotaLog
     */
    public static function create(LogisticsDailyQuota $logisticsDailyQuota = null)
    {
        $entity = new static();
        if (!empty($logisticsDailyQuota))
        {
            $entity->logistics_id = $logisticsDailyQuota->logistics_id;
        }

        $entity->create_time = new DateTime();

        return $entity;
    }
}
