<?php
/**
 * User: liyu
 * Date: 2018/9/5
 * Time: 15:04
 * Desc: OilPriceItem.php
 */

namespace ddd\OilStation\Domain\OilPrice;


use ddd\OilStation\Domain\OilCommonEntity;

class OilPriceItem extends OilCommonEntity{
    #region property

    /**
     * 标识
     * @var   int
     */
    protected $item_id = 0;

    /**
     * 油企
     * @var   int
     */
    protected $company_id = 0;

    /**
     * 油站
     * @var   int
     */
    protected $station_id = 0;

    /**
     * 油品
     * @var   int
     */
    protected $goods_id = 0;

    /**
     * 零售价
     * @var   int
     */
    protected $retail_price = 0;

    /**
     * 协议价
     * @var   int
     */
    protected $agreed_price = 0;

    /**
     * 优惠价
     * @var   int
     */
    protected $discount_price = 0;

    #endregion

    #region

    /**
     * @return int
     */
    public function getItemId():int{
        return $this->item_id;
    }

    /**
     * @param int $item_id
     */
    public function setItemId(int $item_id):void{
        $this->item_id = $item_id;
    }

    /**
     * @return int
     */
    public function getCompanyId():int{
        return $this->company_id;
    }

    /**
     * @param int $company_id
     */
    public function setCompanyId(int $company_id):void{
        $this->company_id = $company_id;
    }

    /**
     * @return int
     */
    public function getStationId():int{
        return $this->station_id;
    }

    /**
     * @param int $station_id
     */
    public function setStationId(int $station_id):void{
        $this->station_id = $station_id;
    }

    /**
     * @return int
     */
    public function getGoodsId():int{
        return $this->goods_id;
    }

    /**
     * @param int $goods_id
     */
    public function setGoodsId(int $goods_id):void{
        $this->goods_id = $goods_id;
    }

    /**
     * @return int
     */
    public function getRetailPrice():int{
        return $this->retail_price;
    }

    /**
     * @param int $retail_price
     */
    public function setRetailPrice(int $retail_price):void{
        $this->retail_price = $retail_price;
    }

    /**
     * @return int
     */
    public function getAgreedPrice():int{
        return $this->agreed_price;
    }

    /**
     * @param int $agreed_price
     */
    public function setAgreedPrice(int $agreed_price):void{
        $this->agreed_price = $agreed_price;
    }

    /**
     * @return int
     */
    public function getDiscountPrice():int{
        return $this->discount_price;
    }

    /**
     * @param int $discount_price
     */
    public function setDiscountPrice(int $discount_price):void{
        $this->discount_price = $discount_price;
    }

    #endregion

    #region logic methods

    /**
     * 创建
     * @return   OilPriceItem
     */
    public static function create(){
        // TODO: implement
        return new static();
    }

    /**
     * 是否可用油价
     * @return   boolean
     */
    public function isActive(){
        // TODO: implement
    }

    /**
     * 油价校验
     */
    public function checkOilPrice(){
        // TODO: implement
    }

    /**
     * 是否可生效
     * @return   boolean
     */
    public function isCanEffect(){
        // TODO: implement
    }

    /**
     * 设为生效
     */
    public function setEffect(){
        // TODO: implement
    }

    /**
     * 设为失效
     */
    public function setTrash(){
        // TODO: implement
    }

    #endregion
}