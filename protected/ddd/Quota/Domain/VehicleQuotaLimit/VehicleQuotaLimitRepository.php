<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/5 0005
 * Time: 10:27
 */

namespace ddd\Quota\Domain\VehicleQuotaLimit;


use ddd\Infrastructure\DIService;

trait VehicleQuotaLimitRepository
{
    /**
     * @var IVehicleQuotaLimitRepository
     */
    protected $vehicleQuotaLimitRepository;

    /**
     * @desc 获取仓储
     * @return IVehicleQuotaLimitRepository
     * @throws \Exception
     */
    protected function getVehicleQuotaLimitRepository()
    {
        if (empty($this->vehicleQuotaLimitRepository))
        {
            $this->vehicleQuotaLimitRepository = DIService::getRepository(IVehicleQuotaLimitRepository::class);
        }

        return $this->vehicleQuotaLimitRepository;
    }
}