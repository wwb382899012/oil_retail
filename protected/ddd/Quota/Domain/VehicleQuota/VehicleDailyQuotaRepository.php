<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/4 0004
 * Time: 10:35
 */

namespace ddd\Quota\Domain\VehicleQuota;


use ddd\Infrastructure\DIService;

trait VehicleDailyQuotaRepository
{
    /**
     * @var IVehicleDailyQuotaRepository
     */
    protected $vehicleDailyQuotaRepository;

    /**
     * @desc 获取车辆当日额度仓储
     * @return IVehicleDailyQuotaRepository
     * @throws \Exception
     */
    public function getVehicleDailyQuotaRepository()
    {
        if(empty($this->vehicleDailyQuotaRepository)) {
            $this->vehicleDailyQuotaRepository = DIService::getRepository(IVehicleDailyQuotaRepository::class);
        }

        return $this->vehicleDailyQuotaRepository;
    }
}