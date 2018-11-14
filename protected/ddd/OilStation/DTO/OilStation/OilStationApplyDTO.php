<?php

namespace ddd\OilStation\DTO\OilStation;


use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\OilStation\Application\OilStationApplyService;
use ddd\OilStation\Domain\OilStation\OilStationApply;
use ddd\OilStation\Domain\Value\Area;
use ddd\OilStation\Domain\Value\Company;
use ddd\OilStation\Domain\Value\Contact;
use ddd\OilStation\Domain\Value\Position;
use ddd\OilStation\DTO\OilFileDto;

class OilStationApplyDTO extends OilFileDto{
    #region property

    /**
     * 标识
     * @var   int
     */
    public $apply_id = 0;

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

    public $is_can_submit = false;

    public $is_can_audit = false;

    #endregion

    public function rules(){
        return [
            ["name", "required", "message" => "请填写油站名称！"],
            ["name", "validateName"],
            ["company_id", "numerical", "integerOnly" => true, "min" => 1, "tooSmall" => "请选择所属企业！"],
            ["address", "required", "message" => "请填写详细地址"],
            ["province_id", "numerical", "integerOnly" => true, "min" => 1, "tooSmall" => "信息异常，缺少必要参数省份id！"],
            ["city_id", "numerical", "integerOnly" => true, "min" => 1, "tooSmall" => "信息异常，缺少必要参数城市id！"],
            ["longitude",'numerical', "min" => 0.0000000001, 'max'=> 360, "tooSmall" => "经度不能小于0度,且有效小数位10位！","tooBig" => "经度不能大于360度"],
            ["latitude",'numerical', "min" => 0.0000000001, 'max'=> 360, "tooSmall" => "纬度不能小于0度,且有效小数位10位！","tooBig" => "纬度不能大于360度"],
            ["files",'validateFiles'],
        ];
    }

    /**
     * @param BaseEntity $entity
     * @throws \Exception
     */
    public function fromEntity(BaseEntity $entity):void {
        $this->entityToDto($entity);
    }

    public function entityToDto(OilStationApply $entity){
        $this->apply_id = $entity->getId();
        $this->name  = $entity->getName();
        $this->company_id = $entity->getCompanyId();
        $this->company_name = $entity->getCompanyName();
        $this->province_id = $entity->getProvinceId();
        $this->province_name = $entity->getProvinceName();
        $this->city_id = $entity->getCityId();
        $this->city_name = $entity->getCityName();
        $this->address = $entity->getAddress();
        $this->longitude = $entity->getLongitude();
        $this->latitude = $entity->getLatitude();
        $this->contact_person = $entity->getContactName();
        $this->contact_phone = $entity->getContactPhone();
        $this->is_can_submit = $entity->isCanEdit();
        $this->is_can_audit = $entity->isOnChecking();
        $this->files = [];

        parent::fromEntity($entity);
    }

    public function toEntity():OilStationApply{
        $entity = new OilStationApply();
        $entity->setId($this->apply_id);
        $entity->setName($this->name);
        $entity->setCompany(new Company($this->company_id,$this->company_name));
        $entity->setProvince(new Area($this->province_id,$this->province_name));
        $entity->setCity(new Area($this->city_id,$this->city_name));
        $entity->setAddress($this->address);
        $entity->setPosition(new Position($this->longitude,$this->latitude));
        $entity->setContact(new Contact($this->contact_person,$this->contact_phone));
        $entity->setRemark($this->remark);
        $entity->setStatus(new Status($this->status,'', \Map::getStatusName('oil_station_apply_status',$this->status_name)));

        $this->setFilesToEntity($entity);

        return $entity;
    }

    /**
     * @param $attribute
     * @return bool
     * @throws \Exception
     */
    public function validateName($attribute):bool {
        if(empty($this->name)){
            return true;
        }

        $isExist = OilStationApplyService::service()->checkNameIsExist($this->apply_id,trim($this->name));
        if($isExist){
            $this->addError($attribute,"油站名称已被占用！");
            return false;
        }

        return true;
    }

    /**
     * @param $attributes
     * @return bool
     */
    public function validateFiles($attributes):bool {
        if(\CheckUtility::isEmpty($this->files)){
            return true;
        }

        foreach($this->files as & $fileDto){
            if(!$fileDto->validate()){
                $this->addError($attributes,$fileDto->getErrors());
                return false;
            }
        }

        return true;
    }
}