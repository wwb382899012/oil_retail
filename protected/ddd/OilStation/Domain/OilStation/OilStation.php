<?php
/**
 * User: liyu
 * Date: 2018/9/5
 * Time: 15:01
 * Desc: 油站
 */

namespace ddd\OilStation\Domain\OilStation;


use app\ddd\Common\Domain\Value\Status;
use ddd\OilStation\Domain\OilFileEntity;
use ddd\OilStation\Domain\Value\Area;
use ddd\OilStation\Domain\Value\Company;
use ddd\OilStation\Domain\Value\Contact;
use ddd\OilStation\Domain\Value\Position;

class OilStation extends OilFileEntity{

    use TraitOilStationRepository;

    #region property

    /**
     * 标识
     * @var   int
     */
    protected $station_id = 0;

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

    /**
     * 二维码
     * @var   string
     */
    protected $qr_code;

    /**
     * @return int
     */
    public function getId():int{
        return $this->station_id;
    }

    /**
     * @param int $stationId
     */
    public function setId(int $stationId):void{
        $this->station_id = $stationId;
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
     * 是否可用油站
     */
    public function isActive(){
        return $this->getStatusValue() == OilStationEnum::ENABLE;
    }

    /**
     * 添加二维码
     */
    public function addQrCode(){
        // TODO: implement
    }

    /**
     * 获取二维码
     * @return   string
     */
    public function getQrCode(){
        // TODO: implement
    }

    /**
     * @param bool $persistent
     * @return OilStation
     * @throws \Exception
     */
    public function save($persistent = true):OilStation{
        if($persistent){
            $this->getOilStationRepository()->store($this);
        }

        return $this;
    }

    /**
     * 设为启用,禁用
     * @param bool $state
     * @param bool $persistent
     * @return $this
     * @throws \Exception
     */
    public function setOnOff(bool $state,$persistent = true):OilStation{
        $status_value = $state ? OilStationEnum::ENABLE : OilStationEnum::UNABLE;
        $this->setStatus(new Status($status_value));

        if($persistent){
            $this->getOilStationRepository()->updateStatus($this);
        }

        return $this;
    }

    #endregion
}