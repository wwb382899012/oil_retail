<?php
/**
 * User: liyu
 * Date: 2018/9/5
 * Time: 15:03
 * Desc: OilPrice.php
 */

namespace ddd\OilStation\Domain\OilPrice;


use ddd\Common\Domain\Value\DateTime;
use ddd\Common\IAggregateRoot;
use ddd\OilStation\Domain\OilCommonEntity;
use ddd\OilStation\Domain\Value\Company;
use ddd\OilStation\Domain\Value\Goods;
use ddd\OilStation\Domain\Value\Station;

class OilPrice extends OilCommonEntity implements IAggregateRoot{


    use TraitOilPriceRepository;

    #region property

    /**
     * 标识
     * @var   int
     */
    protected $price_id = 0;

    /**
     * 申请id
     * @var   int
     */
    protected $apply_id = 0;

    protected $item_id = 0;

    /**
     * 油企
     * @var   Company
     */
    protected $company;

    /**
     * 油站
     * @var   Station
     */
    protected $station;

    /**
     * 油品
     * @var   Goods
     */
    protected $goods;

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

    /**
     * 失效时间
     * @var   Datetime
     */
    protected $end_time;

    #endregion

    #region get set methods
    public function getId(){
        return $this->price_id;
    }

    public function setId($value){
        $this->price_id = $value;
    }

    /**
     * @return int
     */
    public function getApplyId():int{
        return $this->apply_id;
    }

    /**
     * @param int $apply_id
     */
    public function setApplyId(int $apply_id):void{
        $this->apply_id = $apply_id;
    }

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
     * @return Company
     */
    public function getCompany():Company{
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company):void{
        $this->company = $company;
    }

    /**
     * @return Station
     */
    public function getStation():Station{
        return $this->station;
    }

    /**
     * @param Station $station
     */
    public function setStation(Station $station):void{
        $this->station = $station;
    }

    /**
     * @return Goods
     */
    public function getGoods():Goods{
        return $this->goods;
    }

    /**
     * @param Goods $goods
     */
    public function setGoods(Goods $goods):void{
        $this->goods = $goods;
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

    /**
     * @return Datetime
     */
    public function getEndTime():Datetime{
        return $this->end_time;
    }

    /**
     * @param Datetime $end_time
     */
    public function setEndTime(Datetime $end_time):void{
        $this->end_time = $end_time;
    }

    #endregion

    #region get ext methods

    public function getCompanyId(){
        return $this->company->getId();
    }

    public function getCompanyName(){
        return $this->company->getName();
    }

    public function getStationId(){
        return $this->station->getId();
    }

    public function getStationName(){
        return $this->station->getName();
    }

    public function getGoodsId():int{
        return $this->goods->getId();
    }

    public function getGoodsName():string{
        return $this->goods->getName();
    }

    #endregion


    #region logic methods

    /**
     * 创建
     * @return   OilPrice
     */
    public static function create(){
        return new static();
    }

    /**
     * 是否可用油价
     * @return   boolean
     */
    public function isActive(){
        $now = new DateTime();
        return ($this->effect_time <= $now && $this->end_time >= $now);

        //return $this->status->status==OilPriceEnum::STATUS_ACTIVE;
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

    /**
     * 设置失效日期
     * @param DateTime|null $datetime
     * @param bool          $persistent
     * @throws \Exception
     */
    public function setInvalidDate(DateTime $datetime = null, $persistent = true){
        if(empty($datetime))
            $datetime = new DateTime(date("Y-m-d")." 23:59:59");
        $this->end_time = $datetime;
        if($persistent){
            $this->getOilPriceRepository()->saveEndTime($this);
        }
    }

    /**
     * @param bool $persistent
     * @throws \Exception
     */
    public function save($persistent = true):void {
        if($persistent){
            $this->getOilPriceRepository()->store($this);
        }
    }

    #endregion
}
