<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/11 0011
 * Time: 9:36
 */

namespace ddd\Quota\Application\VehicleQuota;


use ddd\Common\Application\BaseService;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\Utility;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Infrastructure\error\ZException;
use ddd\Quota\DTO\VehicleQuota\VehicleDailyQuotaDTO;
use ddd\Quota\Domain\VehicleQuota\IVehicleDailyQuotaRepository;

class VehicleDailyQuotaService extends BaseService
{
    /**
     * 获取车辆当日额度信息
     * @param $vehicleId
     * @param $date
     * @return VehicleDailyQuotaDTO
     * @throws \Exception
     */
    public function getVehicleDailyQuota($vehicleId, $date = '')
    {
        if (!isset($vehicleId) || !\Utility::checkQueryId($vehicleId) || $vehicleId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'vehicleId']);
        }

        try
        {
            if (empty($date))
            {
                $date = Utility::getDate();
            }

            $entity = DIService::getRepository(IVehicleDailyQuotaRepository::class)->findByVehicleId($vehicleId, $date);
            if (empty($entity))
            {
                ExceptionService::throwBusinessException(BusinessError::Vehicle_Daily_Quota_Not_Exist, ['vehicle_id' => $vehicleId]);
            }

            $dto = new VehicleDailyQuotaDTO();
            $dto->fromEntity($entity);

            return $dto;
        } catch (\Exception $e)
        {
            throw new ZException($e->getMessage(), $e->getCode());
        }
    }
}