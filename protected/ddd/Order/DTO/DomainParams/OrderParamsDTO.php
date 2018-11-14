<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/7 0007
 * Time: 17:52
 */

namespace ddd\Order\DTO\DomainParams;

use ddd\Common\Application\BaseDTO;

class OrderParamsDTO extends BaseDTO
{
    /**
     * 客户id
     * @var int
     */
    public $customer_id = 0;

    /**
     * 用户交易密码
     * @var string
     */
    public $customer_trans_password;

    /**
     * 车辆id
     * @var int
     */
    public $vehicle_id = 0;

    /**
     * 油站id
     * @var int
     */
    public $station_id = 0;

    /**
     * 油品id
     * @var int
     */
    public $goods_id = 0;

    /**
     * 升数
     * @var int
     */
    public $quantity = 0;

    /**
     * 采购价
     * @var int
     */
    public $price_buy = 0;

    /**
     * 销售价
     * @var int
     */
    public $price_sell = 0;

    /**
     * 零售价
     * @var int
     */
    public $price_retail = 0;

    /**
     * 备注
     * @var string
     */
    public $remark = '';

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('customer_id, customer_trans_password, vehicle_id, station_id, goods_id, quantity, price_buy, price_sell, price_retail', "required", "message" => "{attribute}字段不得为空！"),
            array('customer_id, vehicle_id, station_id, goods_id, price_buy, price_sell, price_retail', 'numerical', 'integerOnly'=>true),
            array('quantity', 'length', 'max'=>20),
        );
    }
}