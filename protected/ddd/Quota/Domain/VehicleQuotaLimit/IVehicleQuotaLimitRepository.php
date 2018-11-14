<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/4 0004
 * Time: 16:07
 */

namespace ddd\Quota\Domain\VehicleQuotaLimit;


use ddd\Common\Domain\IRepository;

interface IVehicleQuotaLimitRepository extends IRepository
{
    /**
     * 获取车辆当前可用车辆限额设置
     * @return   VehicleQuotaLimit
     */
    public function getActiveVehicleQuotaLimit();
}