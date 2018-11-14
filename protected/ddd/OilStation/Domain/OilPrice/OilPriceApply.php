<?php
/**
 * User: liyu
 * Date: 2018/9/5
 * Time: 15:03
 * Desc: OilPriceApply.php
 */

namespace ddd\OilStation\Domain\OilPrice;


use app\ddd\Common\Domain\Value\Status;
use ddd\OilStation\Domain\OilFileEntity;
use ddd\OilStation\Domain\OilPrice\Event\OilPriceApplyBackedEvent;
use ddd\OilStation\Domain\OilPrice\Event\OilPriceApplyPassedEvent;
use ddd\OilStation\Domain\OilPrice\Event\OilPriceApplySubmittedEvent;

class OilPriceApply extends OilFileEntity{

    /**
     * 提交事件
     */
    const EVENT_AFTER_SUBMIT = "onAfterSubmit";
    /**
     * 驳回事件
     */
    const EVENT_AFTER_BACK = "onAfterBack";
    /**
     * 提交事件
     */
    const EVENT_AFTER_PASS = "onAfterPass";

    use TraitOilPriceApplyRepository;

    #region property

    /**
     * 标识
     * @var   int
     */
    protected $apply_id = 0;

    /**
     * 编号
     * @var   string
     */
    protected $code;

    /**
     * 油价明细项
     * @var   OilPriceItem[]
     */
    protected $items = [];

    #endregion

    /**
     * 验证价格子项
     */
    protected function afterValidate(){
        parent::afterValidate();
        if(is_array($this->items)){
            foreach($this->items as $item){
                $res = $item->validate();
                if(!$res)
                    $this->addError("items", $item->getErrors());
            }
        }
    }


    /**
     * 事件配置，事件名必须以on开头，否则无效
     * @return array
     */
    protected function events(){
        return [
            static::EVENT_AFTER_SUBMIT,
            static::EVENT_AFTER_BACK,
            static::EVENT_AFTER_PASS,
        ];
    }

    #region get set methods

    /**
     * @return int
     */
    public function getId():int{
        return $this->apply_id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id):void{
        $this->apply_id = $id;
    }

    /**
     * @return string
     */
    public function getCode():string{
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code):void{
        $this->code = $code;
    }

    /**
     * @return OilPriceItem[]
     */
    public function getItems():array{
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems(array $items):void{
        $this->items = $items;
    }

    #endregion

    /**
     * 创建
     * @return   OilPriceApply
     */
    public static function create():OilPriceApply{
        // TDO: implement
    }

    /**
     * 添加油价明细
     * @param OilPriceItem $priceEntity
     */
    public function addOilPrice(OilPriceItem $priceEntity){
        $key = $priceEntity->getStationId().''.$priceEntity->getGoodsId();
        $this->items[$key] = $priceEntity;
    }

    public function clearOilPrice(){
        $this->items = [];
    }

    /**
     * 油价明细是否存在
     * @return   boolean
     */
    public function oilPriceIsExists(){
        // TODO: implement
    }

    /**
     * 是否可修改
     * @return   boolean
     */
    public function isCanEdit():bool{
        return $this->getStatusValue() < OilPriceApplyEnum::STATUS_SUBMIT;
    }

    /**
     * 提交
     * @param bool $persistent
     * @throws \Exception
     */
    public function submit(bool $persistent = true){
        $this->status = new Status(OilPriceApplyEnum::STATUS_SAVED, \DateUtility::getDateTime(), '');
        if($persistent){
            $this->getOilPriceApplyRepository()->store($this);
        }

        $this->publishEvent(static::EVENT_AFTER_SUBMIT, new OilPriceApplySubmittedEvent($this));
    }

    /**
     * 驳回
     * @param $persistent
     * @throws \Exception
     */
    public function reject($persistent = true){
        $this->status = new Status(OilPriceApplyEnum::STATUS_BACKED, \DateUtility::getDateTime(), '');
        if($persistent){
            $this->getOilPriceApplyRepository()->updateStatus($this);
        }

        $this->publishEvent(static::EVENT_AFTER_BACK, new OilPriceApplyBackedEvent($this));
    }

    /**
     * 通过
     * @param $persistent
     * @throws \Exception
     */
    public function checkPass($persistent = true){
        $this->status = new Status(OilPriceApplyEnum::STATUS_PASSED, \DateUtility::getDateTime(), '');
        if($persistent){
            $this->getOilPriceApplyRepository()->updateStatus($this);
        }

        $this->publishEvent(static::EVENT_AFTER_PASS, new OilPriceApplyPassedEvent($this));
    }

    /**
     * 是否可用油价申请
     * @return   boolean
     */
    public function isActive(){
        // TODO: implement
    }

}