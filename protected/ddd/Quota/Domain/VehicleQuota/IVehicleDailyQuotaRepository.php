<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 15:39
 */

namespace ddd\Quota\Domain\VehicleQuota;


use ddd\Quota\Domain\IQuotaRepository;

interface IVehicleDailyQuotaRepository extends IQuotaRepository
{
    /**
     * 根据车辆id获取容量信息
     * @param $vehicle_id
     * @param string $date
     * @return mixed
     */
    public function findByVehicleId($vehicle_id, $date = '');
}