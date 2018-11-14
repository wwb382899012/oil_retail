<?php

namespace ddd\OilStation\DTO\OilStation;


use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\OilStation\Domain\OilStation\OilStation;
use ddd\OilStation\Domain\Value\Area;
use ddd\OilStation\Domain\Value\Company;
use ddd\OilStation\Domain\Value\Contact;
use ddd\OilStation\Domain\Value\Position;
use ddd\OilStation\DTO\OilFileDto;

class OilStationDTO extends OilFileDto{
    #region property

    /**
     * 标识
     * @var   int
     */
    public $station_id = 0;

    /**
     * 油站名称
     * @var   string
     */
    public $name;

    /**
     * 油企
     * @var   int
     */
    public $company_id = 0;

    /**
     * 油企
     * @var   string
     */
    public $company_name = '';

    /**
     * 所在省
     * @var   int
     */
    public $province_id = 0;

    public $province_name = '';

    /**
     * 所在城市
     * @var   int
     */
    public $city_id = 0;

    public $city_name = '';

    /**
     * 地址
     * @var   string
     */
    public $address;

    /**
     *
     * @var   string
     */
    public $longitude = '';

    public $latitude = '';

    /**
     * 联系人
     * @var   string
     */
    public $contact_person = '';

    public $contact_phone = '';

    /**
     * 备注
     * @var   string
     */
    public $remark = '';

    /**
     * 状态
     * @var   int
     */
    public $status = 0;

    public $status_name = '';

    /**
     * 附件
     * @var   array
     */
    public $files = [];

    #endregion

    public function fromEntity(BaseEntity $entity):void {
        $this->entityToDto($entity);
    }

    private function entityToDto(OilStation $entity){
        $this->station_id = $entity->getId();
        $this->name  = $entity->getName();
        $this->company_id = $entity->getCompanyId();
        $this->company_name = $entity->getCompanyName();
        $this->province_id = $entity->getProvinceId();
        $this->province_name = $entity->getProvinceName();
        $this->city_id = $entity->getCityId();
        $this->city_name = $entity->getCityName();
        $this->address = empty($entity->getAddress()) ? '' : $entity->getAddress();
        $this->longitude = (empty($entity->getLongitude()) || 0 ==$entity->getLongitude()) ? '' : $entity->getLongitude();
        $this->latitude = empty($entity->getLatitude()) || 0 ==$entity->getLatitude() ? '' : $entity->getLatitude();
        $this->contact_person = $entity->getContactName();
        $this->contact_phone = $entity->getContactPhone();
        $this->files = [];

        parent::fromEntity($entity);
    }

    public function toEntity():OilStation{
        $entity = new OilStation();
        $entity->setId($this->station_id);
        $entity->setName($this->name);
        $entity->setCompany(new Company($this->company_id,$this->company_name));
        $entity->setProvince(new Area($this->province_id,$this->province_name));
        $entity->setCity(new Area($this->city_id,$this->city_name));
        $entity->setAddress($this->address);
        $entity->setPosition(new Position($this->longitude,$this->latitude));
        $entity->setContact(new Contact($this->contact_person,$this->contact_phone));
        $entity->setRemark($this->remark);
        $entity->setStatus(new Status($this->status,'', \Map::getStatusName('oil_station_status',$this->status_name)));

        $this->setFilesToEntity($entity);

        return $entity;
    }
}