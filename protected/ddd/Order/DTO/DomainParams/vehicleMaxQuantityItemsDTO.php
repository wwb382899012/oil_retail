<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/10 0010
 * Time: 14:16
 */

namespace ddd\Order\DTO\DomainParams;


use ddd\Common\Application\BaseDTO;

class vehicleMaxQuantityItemsDTO extends BaseDTO
{
    public $vehicle_id = 0;

    public $vehicle_number;

    public $vehicle_model;

    public $max_available_quantity = 0;
}