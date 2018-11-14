<?php

namespace ddd\OilStation\DTO\OilStation;

use ddd\Common\Application\BaseDTO;

use ddd\OilStation\Domain\OilCompany\TraitOilCompanyRepository;
use ddd\OilStation\Domain\OilPrice\OilPrice;
use ddd\OilStation\Domain\OilStation\OilStation;
use ddd\OilStation\Domain\OilStation\OilStationEnum;

class OilStationDetailDTO extends BaseDTO
{
    #region property
    use TraitOilCompanyRepository;

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

    public $province = '';

    /**
     * 所在城市
     * @var   int
     */
    public $city_id = 0;

    public $city = '';

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

    public $closest = 0;

    public $most_visit = 0;

    public $goods = [];

    /**
     * @var 距离
     */
    public $distance;

    public $status;

    #endregion

    public function fromEntity(OilStation $entity, array $oilGoodsEntities) {
        $this->station_id = $entity->getId();
        $this->name = $entity->getName();
        $this->company_id = $entity->getCompanyId();
        $this->company_name = $entity->getCompanyName();
        $this->province_id = $entity->getProvinceId();
        $this->province = $entity->getProvinceName();
        $this->city_id = $entity->getCityId();
        $this->city = $entity->getCityName();
        $this->address = $entity->getAddress();
        $this->longitude = $entity->getLongitude();
        $this->latitude = $entity->getLatitude();
        $this->contact_person = $entity->getContactName();
        $this->contact_phone = $entity->getContactPhone();
        $this->remark = $entity->getRemark();
        $this->status = $entity->getStatus()->status;
        $oilCompany = $this->getOilCompanyRepository()->findById($entity->getCompanyId());
        if (!$oilCompany->isActive()) {//油企禁用  油站也要显示禁用
            $this->status = OilStationEnum::UNABLE;
        }
        $this->goods = [];
        foreach ($oilGoodsEntities as $goodsEntity) {
            $goods_dto = new OilStationGoodsItemDTO();
            $goods_dto->fromEntity($goodsEntity);
            $this->goods[] = $goods_dto;
        }
    }
}