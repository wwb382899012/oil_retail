<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/6 0006
 * Time: 9:50
 */

namespace ddd\Quota\Domain\VehicleQuota;


use app\ddd\Common\Domain\Value\Vehicle;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Logistics\Domain\Vehicle\IVehicleRepository;
use ddd\Quota\Domain\BaseRiskQuotaService;

class VehicleDailyQuotaService extends BaseRiskQuotaService
{
    use VehicleDailyQuotaLogRepository;
    use VehicleDailyQuotaRepository;

    public function init()
    {
        $this->logRepository = $this->getVehicleDailyQuotaLogRepository();
    }

    /**
     * 创建车辆当日额度
     * @param $vehicleId
     * @return mixed
     * @throws \Exception
     */
    public function createVehicleDailyQuota($vehicleId)
    {
        if (empty($vehicleId))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'vehicle_id']);
        }
        $vehicleEntity = DIService::getRepository(IVehicleRepository::class)->findById($vehicleId);
        if (empty($vehicleEntity))
        {
            ExceptionService::throwBusinessException(BusinessError::Vehicle_Not_Exist, ['vehicle_id' => $vehicleId]);
        }

        $vehicleValueEntity = new Vehicle($vehicleId);
        $entity = VehicleDailyQuota::create($vehicleValueEntity);

        $entity = $this->getVehicleDailyQuotaRepository()->store($entity);

        return $entity;
    }
}