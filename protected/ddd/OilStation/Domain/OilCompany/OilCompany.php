<?php
/**
 * User: liyu
 * Date: 2018/9/5
 * Time: 15:00
 * Desc: OilCompany.php
 */

namespace ddd\OilStation\Domain\OilCompany;


use ddd\Common\Domain\Value\DateTime;
use ddd\OilStation\Domain\OilFileEntity;
use ddd\OilStation\Domain\Value\Ownership;

class OilCompany extends OilFileEntity{

    use TraitOilCompanyRepository;

    const EVENT_AFTER_UNABLE = 'onAfterUnable';

    #region property

    /**
     * 标识
     * @var   int
     */
    protected $id = 0;

    /**
     * 企业名称
     * @var   string
     */
    protected $name;

    /**
     * 企业简称
     * @var   string
     */
    protected $short_name;

    /**
     * 纳税人识别号
     * @var   string
     */
    protected $tax_code;

    /**
     * 法人代表
     * @var   string
     */
    protected $corporate;

    /**
     * 地址
     * @var   string
     */
    protected $address;

    /**
     * 联系电话
     * @var   string
     */
    protected $contact_phone;

    /**
     * 企业所有制
     * @var   Ownership
     */
    protected $ownership;

    /**
     * 成立日期
     * @var   Datetime
     */
    protected $build_date;

    #endregion

    /**
     * 事件配置，事件名必须以on开头，否则无效
     * @return array
     */
    protected function events(){
        return [
            static::EVENT_AFTER_UNABLE,
        ];
    }

    #region get set methods

    /**
     * @return int
     */
    public function getId():int{
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id):void{
        $this->id = $id;
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
     * @return string
     */
    public function getShortName():string{
        return $this->short_name;
    }

    /**
     * @param string $short_name
     */
    public function setShortName(string $short_name):void{
        $this->short_name = $short_name;
    }

    /**
     * @return string
     */
    public function getTaxCode():string{
        return $this->tax_code;
    }

    /**
     * @param string $tax_code
     */
    public function setTaxCode(string $tax_code):void{
        $this->tax_code = $tax_code;
    }

    /**
     * @return string
     */
    public function getCorporate():string{
        return $this->corporate;
    }

    /**
     * @param string $corporate
     */
    public function setCorporate(string $corporate):void{
        $this->corporate = $corporate;
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
     * @return string
     */
    public function getContactPhone():string{
        return $this->contact_phone;
    }

    /**
     * @param string $contact_phone
     */
    public function setContactPhone(string $contact_phone):void{
        $this->contact_phone = $contact_phone;
    }

    /**
     * @return Ownership
     */
    public function getOwnership():Ownership{
        return $this->ownership;
    }

    /**
     * @param Ownership $ownership
     */
    public function setOwnership(Ownership $ownership):void{
        $this->ownership = $ownership;
    }

    /**
     * @return DateTime
     */
    public function getBuildDate():?DateTime{
        return $this->build_date;
    }

    /**
     * @param DateTime $build_date
     */
    public function setBuildDate(DateTime $build_date):void{
        $this->build_date = $build_date;
    }

    #endregion

    #region get set methods

    public function getOwnershipValue():int{
        return $this->ownership->getId();
    }

    public function getOwnershipName():string {
        return $this->ownership->getName();
    }

    #endregion

    #region logic method

    /**
     * 创建
     * @return   OilCompany
     */
    public static function create(){
        // TODO: implement
    }

    /**
     * 是否可用油企
     * @return   boolean
     */
    public function isActive():bool{
        return OilCompanyStatusEnum::ENABLE == $this->getStatusValue();
    }

    /**
     * @param bool $persistent
     * @throws \Exception
     */
    public function save($persistent = true){
        if($persistent){
            $this->getOilCompanyRepository()->store($this);
        }

        if(!$this->isActive()){
            $this->publishEvent(static::EVENT_AFTER_UNABLE, new OilCompanyUnableEvent($this));
        }
    }

    #endregion
}