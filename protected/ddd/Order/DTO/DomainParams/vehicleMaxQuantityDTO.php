<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/10 0010
 * Time: 16:56
 */

namespace ddd\Order\DTO\DomainParams;


use ddd\Common\Application\BaseDTO;

class vehicleMaxQuantityDTO extends BaseDTO
{
    /**
     * 品名id
     * @var int
     */
    public $goods_id = 0;

    /**
     * 品名
     * @var string
     */
    public $goods_name;

    /**
     * 车辆最大可加油数列表
     * @var vehicleMaxQuantityItemsDTO[]
     */
    public $items = [];
}