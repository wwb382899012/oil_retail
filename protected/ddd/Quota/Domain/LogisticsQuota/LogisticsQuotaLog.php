<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 16:41
 */

namespace ddd\Quota\Domain\LogisticsQuota;


use ddd\Common\Domain\Value\DateTime;
use ddd\Common\IAggregateRoot;

class LogisticsQuotaLog extends BaseLogisticsQuotaLog implements IAggregateRoot
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
     * @param    LogisticsQuota $logisticsQuota
     * @return   LogisticsQuotaLog
     */
    public static function create(LogisticsQuota $logisticsQuota = null)
    {
        $entity = new static();
        if (!empty($logisticsQuota))
        {
            $entity->logistics_id = $logisticsQuota->logistics_id;
        }

        $entity->create_time = new DateTime();

        return $entity;
    }
}