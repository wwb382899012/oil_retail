<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/6 0006
 * Time: 15:03
 */

namespace ddd\Quota\DTO\DomainParams;


use ddd\Common\Application\BaseDTO;

class OrderPaymentParamsDTO extends BaseDTO
{
    /**
     * 物流企业id
     * @var int
     */
    public $logistics_id = 0;

    /**
     * 下单金额
     * @var int
     */
    public $amount = 0;

    /**
     * 车辆id
     * @var int
     */
    public $vehicle_id = 0;

    /**
     * 升数
     * @var float
     */
    public $quantity = 0;

    /**
     * 关联订单id
     * @var int
     */
    public $relation_id = 0;

    /**
     * 事件源类型
     * @var int
     */
    public $category = 0;

    /**
     * 备注
     * @var string
     */
    public $remark = '';
}