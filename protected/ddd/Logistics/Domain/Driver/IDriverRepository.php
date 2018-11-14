<?php
/**
 * Desc: 司机仓储接口
 * User: vector
 * Date: 2018/9/6
 * Time: 17:10
 */

namespace ddd\Logistics\Domain\Driver;

use ddd\Common\Domain\IRepository;


interface IDriverRepository extends IRepository
{
	function updateStatus(Driver $entity);
    function bindVehicle($customerId, $vehicles);
    function clearCache();
    function findById($customer_id);
    // function getAllDriver();
    // function getActiveDriver();
}