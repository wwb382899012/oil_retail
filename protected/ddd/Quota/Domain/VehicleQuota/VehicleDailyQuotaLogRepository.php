<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/4 0004
 * Time: 10:35
 */

namespace ddd\Quota\Domain\VehicleQuota;


use ddd\Infrastructure\DIService;

trait VehicleDailyQuotaLogRepository
{
    /**
     * @var IVehicleDailyQuotaLogRepository
     */
    protected $vehicleDailyQuotaLogRepository;

    /**
     * @desc 获取车辆当日额度变更仓储
     * @return IVehicleDailyQuotaLogRepository
     * @throws \Exception
     */
    public function getVehicleDailyQuotaLogRepository()
    {
        if(empty($this->vehicleDailyQuotaLogRepository)) {
            $this->vehicleDailyQuotaLogRepository = DIService::getRepository(IVehicleDailyQuotaLogRepository::class);
        }

        return $this->vehicleDailyQuotaLogRepository;
    }
}