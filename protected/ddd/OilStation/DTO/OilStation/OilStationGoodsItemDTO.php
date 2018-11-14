<?php

namespace ddd\OilStation\DTO\OilStation;


use app\ddd\Order\Domain\Goods\Goods;
use ddd\Common\Application\BaseDTO;
use ddd\OilStation\Domain\OilPrice\OilPrice;

class OilStationGoodsItemDTO extends BaseDTO
{

    #region property

    /**
     * 标识
     * @var   int
     */
    public $goods_id = 0;

    /**
     * 名称
     * @var   string
     */
    public $name = '';

    /**
     * 零售价
     * @var int
     */
    public $retail_price = 0;

    /**
     * 协议价
     * @var int
     */
    public $agreed_price = 0;

    /**
     * 优惠价
     * @var int
     */
    public $discount_price = 0;

    #endregion

    public function fromEntity(Goods $entity) {
        $this->goods_id = $entity->goods_id;
        $this->name = $entity->goods_name;
        $this->retail_price = $entity->price_retail;
        $this->agreed_price = $entity->price_buy;
        $this->discount_price = $entity->price_sell;
    }
}