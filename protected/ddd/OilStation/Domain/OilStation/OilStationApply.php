<?php
/**
 * User: liyu
 * Date: 2018/9/5
 * Time: 15:01
 * Desc: 油站准入
 */

namespace ddd\OilStation\Domain\OilStation;


use app\ddd\Common\Domain\Value\Status;
use ddd\OilStation\Domain\OilFileEntity;
use ddd\OilStation\Domain\OilStation\Event\OilStationApplyBackedEvent;
use ddd\OilStation\Domain\OilStation\Event\OilStationApplyPassedEvent;
use ddd\OilStation\Domain\OilStation\Event\OilStationApplySubmittedEvent;
use ddd\OilStation\Domain\Value\Area;
use ddd\OilStation\Domain\Value\Company;
use ddd\OilStation\Domain\Value\Contact;
use ddd\OilStation\Domain\Value\Position;

class OilStationApply extends OilFileEntity{

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

    use TraitOilStationApplyRepository;

    #region property

    /**
     * 标识
     * @var   int
     */
    protected $apply_id = 0;

    /**
     * 油站名称
     * @var   string
     */
    protected $name;

    /**
     * 油企
     * @var   Company
     */
    protected $company = 0;

    /**
     * 所在省
     * @var   Area
     */
    protected $province;

    /**
     * 所在城市
     * @var   Area
     */
    protected $city;

    /**
     * 地址
     * @var   string
     */
    protected $address;

    /**
     * 位置
     * @var   Position
     */
    protected $position;

    /**
     * 联系人
     * @var   Contact
     */
    protected $contact;

    #endregion

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
     * @param int $applyId
     */
    public function setId(int $applyId):void{
        $this->apply_id = $applyId;
    }

    /**
     * @return string
     */
    public function getName():string{
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name):void{
        $this->name = $name;
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
     * @return Area
     */
    public function getProvince():Area{
        return $this->province;
    }

    /**
     * @param Area $province
     */
    public function setProvince(Area $province):void{
        $this->province = $province;
    }

    /**
     * @return Area
     */
    public function getCity():Area{
        return $this->city;
    }

    /**
     * @param Area $city
     */
    public function setCity(Area $city):void{
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getAddress():string{
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address):void{
        $this->address = $address;
    }

    /**
     * @return Position
     */
    public function getPosition():Position{
        return $this->position;
    }

    /**
     * @param Position $position
     */
    public function setPosition(Position $position):void{
        $this->position = $position;
    }

    /**
     * @return Contact
     */
    public function getContact():Contact{
        return $this->contact;
    }

    /**
     * @param Contact $contact
     */
    public function setContact(Contact $contact):void{
        $this->contact = $contact;
    }

    #endregion

    #region get ext methods

    public function getCompanyId():int{
        return $this->company->getId();
    }

    public function getCompanyName():string {
        return $this->company->getName();
    }

    public function getProvinceId():int{
        return $this->province->getCode();
    }

    public function getProvinceName():string {
        return $this->province->getName();
    }

    public function getCityId():int{
        return $this->city->getCode();
    }

    public function getCityName():string {
        return $this->city->getName();
    }

    public function getLongitude():string{
        return $this->position->getLongitude();
    }

    public function getLatitude():string{
        return $this->position->getLatitude();
    }

    public function getContactName():string{
        return $this->contact->getName();
    }

    public function getContactPhone():string {
        return $this->contact->getMobile();
    }

    #endregion

    #region logic methods

    /**
    * 创建
    * @return   OilStationApply
    */
    public static function create(){
        return new static();
    }

    /**
     * 是否可修改
     * @return   bool
     */
    public function isCanEdit():bool{
        return $this->getStatusValue() < OilStationApplyEnum::STATUS_SUBMIT;
    }

    /**
     * 是否再审核中
     * @return   bool
     */
    public function isOnChecking():bool{
        return $this->getStatusValue() == OilStationApplyEnum::STATUS_SUBMIT;
    }

    /**
     * 是否可调整
     * @return   boolean
     */
    public function isCanAdjust(){
        // TODO: implement
    }

    /**
     * @param bool $persistent
     * @throws \Exception
     */
    public function save($persistent = true){
        if($persistent){
            $this->getOilStationApplyRepository()->store($this);
        }
    }

    /**
     * 提交
     * @param $persistent
     * @throws \Exception
     */
    public function setIsSubmitted($persistent = true){
        $this->status = new Status(OilStationApplyEnum::STATUS_PASSED, \DateUtility::getDateTime(),'');

        if($persistent){
            $this->getOilStationApplyRepository()->updateStatus($this);
        }

        $this->publishEvent(static::EVENT_AFTER_SUBMIT, new OilStationApplySubmittedEvent($this));
    }

    /**
     * 驳回
     * @param $persistent
     * @throws \Exception
     */
    public function setIsBacked($persistent = true){
        $this->status = new Status(OilStationApplyEnum::STATUS_BACK, \DateUtility::getDateTime(),'');

        if($persistent){
            $this->getOilStationApplyRepository()->updateStatus($this);
        }

        $this->publishEvent(static::EVENT_AFTER_BACK, new OilStationApplyBackedEvent($this));
    }

    /**
     * 通过
     * @param $persistent
     * @throws \Exception
     */
    public function setIsPassed($persistent = true){
        $this->status = new Status(OilStationApplyEnum::STATUS_PASSED, \DateUtility::getDateTime(),'');

        if($persistent){
            $this->getOilStationApplyRepository()->updateStatus($this);
        }

        $this->publishEvent(static::EVENT_AFTER_PASS, new OilStationApplyPassedEvent($this));
    }

    #endregion
}