<?php
/**
 * Desc: 车辆仓储接口
 * User: vector
 * Date: 2018/9/6
 * Time: 17:10
 */

namespace ddd\Logistics\Domain\Vehicle;

use ddd\Common\Domain\IRepository;


interface IVehicleRepository extends IRepository
{
	function getVehicleIdByNumber($number);
	function updateStatus(Vehicle $entity);
	function clearCache($number="");
	// function getActiveVehicle();
	// function getAllVehicle();
}