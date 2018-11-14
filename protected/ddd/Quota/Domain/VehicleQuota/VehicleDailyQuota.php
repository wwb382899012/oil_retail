<?php
/**
 * Desc: 车辆额度
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 16:30
 */

namespace ddd\Quota\Domain\VehicleQuota;


use app\ddd\Common\Domain\Value\Vehicle;
use app\ddd\Quota\Application\VehicleQuotaLimit\VehicleQuotaLimitService;
use ddd\Common\IAggregateRoot;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Infrastructure\Utility;
use ddd\Logistics\Domain\Vehicle\IVehicleRepository;
use ddd\Quota\Domain\BaseRiskQuota;

class VehicleDailyQuota extends BaseRiskQuota implements IAggregateRoot
{
    /**
     * 车辆id
     * @var   int
     */
    public $vehicle_id = 0;

    /**
     * 当前日期
     * @var date
     */
    public $current_date;

    use VehicleDailyQuotaRepository;

    public function getIdName()
    {
        return "id";
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function init()
    {
        $this->repository = $this->getVehicleDailyQuotaRepository();
    }

    /**
     * 创建
     * @param Vehicle|null $vehicle
     * @return VehicleDailyQuota
     */
    public static function create(Vehicle $vehicle = null)
    {
        $entity = new static();
        if (!empty($vehicle))
        {
            $entity->vehicle_id = $vehicle->id;
        }
        $entity->current_date = Utility::getDate();

        return $entity;
    }

    /**
     * 获取实际授信额度
     */
    public function getActualCreditQuota()
    {
        $activeLimitEntity = DIService::get(VehicleQuotaLimitService::class)->getActiveVehicleQuotaLimit();
        if (empty($activeLimitEntity))
        {
            ExceptionService::throwBusinessException(BusinessError::Vehicle_Quota_Limit_Not_Exist);
        }
        $vehicleEntity = DIService::getRepository(IVehicleRepository::class)->findById($this->vehicle_id);
        if (empty($vehicleEntity))
        {
            ExceptionService::throwBusinessException(BusinessError::Vehicle_Not_Exist, ['vehicle_id' => $this->vehicle_id]);
        }

        return round($vehicleEntity->capacity * $activeLimitEntity->rate);
    }
}