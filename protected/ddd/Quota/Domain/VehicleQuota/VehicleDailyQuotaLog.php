<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 16:43
 */

namespace ddd\Quota\Domain\VehicleQuota;


use ddd\Common\Domain\Value\DateTime;
use ddd\Common\IAggregateRoot;
use ddd\Quota\Domain\BaseRiskQuotaLog;

class VehicleDailyQuotaLog extends BaseRiskQuotaLog implements IAggregateRoot
{
    /**
     * 车辆id
     * @var   int
     */
    public $vehicle_id = 0;

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
     * @param    VehicleDailyQuota $vehicleDailyQuota
     * @return   VehicleDailyQuotaLog
     */
    public static function create(VehicleDailyQuota $vehicleDailyQuota = null)
    {
        $entity = new static();
        if (!empty($vehicleDailyQuota))
        {
            $entity->vehicle_id = $vehicleDailyQuota->vehicle_id;
        }

        $entity->create_time = new DateTime();

        return $entity;
    }

    public function getQuotaObjectPropertyName()
    {
        return "vehicle_id";
    }
}