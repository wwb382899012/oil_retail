<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/11 0011
 * Time: 16:44
 */

namespace ddd\Order\DTO\Order;


use app\ddd\Common\Domain\Value\Customer;
use app\ddd\Common\Domain\Value\LogisticsCompany;
use app\ddd\Common\Domain\Value\OilGoods;
use app\ddd\Common\Domain\Value\OilStation;
use app\ddd\Common\Domain\Value\Vehicle;
use ddd\Common\Application\BaseDTO;

class OrderDTO extends BaseDTO
{
    /**
     * 订单id
     * @var int
     */
    public $order_id;

    /**
     * 订单编号
     * @var string
     */
    public $code;

    /**
     * 状态
     * @var int
     */
    public $status;

    /**
     * 状态描述
     * @var string
     */
    public $status_desc;

    /**
     * 销售价
     * @var int
     */
    public $sell_amount;

    /**
     * 采购价
     * @var int
     */
    public $buy_amount;

    /**
     * 升数
     * @var float
     */
    public $quantity;

    /**
     * 零售价
     * @var float
     */
    public $retail_price;

    /**
     * 协议价
     * @var float
     */
    public $agreed_price;

    /**
     * 优惠价
     * @var float
     */
    public $discount_price;

    /**
     * 订单类型
     * @var datetime
     */
    public $order_type;
    /**
     * 备注
     * @var datetime
     */
    public $remark;
    /**
     * 创建时间
     * @var datetime
     */
    public $create_time;

    /**
     * 生效时间
     * @var datetime
     */
    public $effect_time;

    /**
     * 失败原因
     * @var string
     */
    public $failed_reason;

    /**
     * 油品
     * @var OilGoods
     */
    public $goods;

    /**
     * 司机
     * @var Customer
     */
    public $customer;

    /**
     * 物流企业
     * @var LogisticsCompany
     */
    public $logistics;

    /**
     * 车辆
     * @var Vehicle
     */
    public $vehicle;

    /**
     * 油站
     * @var OilStation
     */
    public $oil_station;

    /**
     * @param \ddd\Common\BaseModel|\ddd\Common\Domain\BaseEntity $entity
     */
    public function fromEntity($entity)
    {
        $this->order_id = $entity->getId();
        $this->code = $entity->code;
        $status = $entity->getStatusValue();
        $this->status = $status;
        $this->status_desc = \Map::getStatusName("order_status", $status);
        $this->sell_amount = $entity->getOrderSellAmount();
        $this->buy_amount = $entity->getOrderBuyAmount();
        $this->retail_price = $entity->price_retail;
        $this->agreed_price = $entity->price_buy;
        $this->discount_price = $entity->price_sell;
        $this->remark = $entity->remark;
        $this->order_type = $entity->order_type;
        $this->create_time = $entity->create_time->format();
        $this->effect_time = !empty($entity->effect_time) ? $entity->effect_time->format() : null;
        $this->failed_reason = $entity->failed_reason;
        $this->goods = $entity->goods;
        $this->quantity = $entity->quantity;
        $this->customer = $entity->customer;
        $this->vehicle = $entity->vehicle;
        $this->oil_station = $entity->oil_station;
        $this->logistics = $entity->logistics;
    }
}