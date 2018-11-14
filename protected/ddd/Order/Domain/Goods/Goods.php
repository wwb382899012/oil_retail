<?php
/**
 * Created by youyi000.
 * DateTime: 2018/9/14 17:16
 * Describe：
 */

namespace app\ddd\Order\Domain\Goods;


use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\OilStation\Application\OilPriceService;

class Goods extends BaseEntity
{
#region property

    /**
     * 标识
     * @var   int
     */
    public $goods_id;

    /**
     * 品名
     * @var   int
     */
    public $goods_name;

    /**
     * 加油站id
     * @var   int
     */
    public $station_id = 0;

    /**
     * 采购价格
     * @var   int
     */
    public $price_buy = 0;

    /**
     * 销售价格
     * @var   int
     */
    public $price_sell = 0;

    /**
     * 零售价格
     * @var   int
     */
    public $price_retail = 0;

    /**
     * 库存数量
     * @var   float
     */
    public $quantity = 0;

    /**
     * 备注
     * @var   string
     */
    public $remark;

    /**
     * 状态
     * @var   Status
     */
    protected $status;

    /**
     * 生效时间
     * @var   DateTime
     */
    public $effect_time;

    /**
     * 创建时间
     * @var   Datetime
     */
    public $create_time;

    /**
     * 更新用户
     * @var   Operator
     */
    public $update_user;

    /**
     * 更新时间
     * @var   Datetime
     */
    public $update_time;

    /**
     * 创建用户
     * @var   Operator
     */
    public $create_user;



    #endregion


    /**
     * 创建可售商品
     * @param $stationId
     * @param $goodsId
     * @return Goods
     * @throws \Exception
     */
    public static function create($stationId,$goodsId)
    {
        if(empty($stationId)) {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name'=>'stationId']);
        }
        if(empty($goodsId)) {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name'=>'$goodsId']);
        }

        $entity=new static();
        $price=DIService::get(OilPriceService::class)->getActivePriceByOilStationAndGoodsId($stationId,$goodsId);
        $entity->goods_id=$goodsId;
        $entity->station_id=$stationId;
        if(!empty($price)) {
            $entity->goods_name=$price->goods->getName();
            $entity->price_buy=$price->getAgreedPrice();
            $entity->price_sell=$price->getDiscountPrice();
            $entity->price_retail=$price->getRetailPrice();
        }
        return $entity;
    }

    public function isActive()
    {
        return $this->price_retail*$this->price_sell*$this->price_buy>0;
    }
}