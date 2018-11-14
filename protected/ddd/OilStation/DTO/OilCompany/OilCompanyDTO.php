<?php

namespace ddd\OilStation\DTO\OilCompany;


use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\OilStation\Application\OilCompanyService;
use ddd\OilStation\Domain\OilCompany\OilCompany;
use ddd\OilStation\Domain\OilCompany\OilCompanyFileEnum;
use ddd\OilStation\Domain\Value\Ownership;
use ddd\OilStation\DTO\OilFileDto;

class OilCompanyDTO extends OilFileDto{

    /**
     * 标识
     * @var   int
     */
    public $company_id = 0;

    /**
     * 企业名称
     * @var   string
     */
    public $name = '';

    /**
     * 企业简称
     * @var   string
     */
    public $short_name = '';

    /**
     * 纳税人识别号
     * @var   string
     */
    public $tax_code = '';

    /**
     * 法人代表
     * @var   string
     */
    public $corporate = '';

    /**
     * 地址
     * @var   string
     */
    public $address = '';

    /**
     * 联系电话
     * @var   string
     */
    public $contact_phone = '';

    /**
     * 企业所有制
     * @var   int
     */
    public $ownership = 0;

    /**
     * 企业所有制
     * @var   string
     */
    public $ownership_name = '';

    /**
     * 成立日期
     * @var   string
     */
    public $build_date = '';

    public function rules(){
        return [
            ['name, short_name, tax_code, corporate, contact_phone','length','max' => 100],
            ["name", "required", "message" => "请填写企业名称"],
            ["name", "validateName"],
            ["short_name", "required", "message" => "请填写企业简称"],
            ["tax_code", "required", "message" => "请填写纳税人识别号"],
            ["tax_code", "validateTaxCode"],
            //["corporate", "required", "message" => "请填写企业法人"],
            //["contact_phone", "required", "message" => "请填写联系方式"],
            //["address", "required", "message" => "请填写联系地址"],
            //["ownership", "numerical", "integerOnly" => true, "min" => 0, "tooSmall" => "信息异常，缺少必要参数企业所有制"],
            ["status", "numerical", "integerOnly" => true, "min" => 0, "tooSmall" => "信息异常，缺少必要参数企业状态"],
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

    private function entityToDto(OilCompany $entity){
        $this->company_id = $entity->getId();
        $this->name = $entity->getName();
        $this->short_name = $entity->getShortName();
        $this->tax_code = $entity->getTaxCode();
        $this->corporate = $entity->getCorporate();
        $this->address = $entity->getAddress();
        $this->contact_phone = $entity->getContactPhone();
        $this->build_date = empty($entity->getBuildDate()) ? '' : $entity->getBuildDate()->toDate();
        $this->ownership = $entity->getOwnershipValue();
        $this->ownership = 0 == $this->ownership ? '' : $this->ownership;
        $this->ownership_name = $entity->getOwnershipName();

        parent::fromEntity($entity);
    }

    /**
     * @return OilCompany
     * @throws \Exception
     */
    public function toEntity():OilCompany{
        $entity = new OilCompany();
        $entity->setId($this->company_id);
        $entity->setName($this->name);
        $entity->setShortName($this->short_name);
        $entity->setTaxCode($this->tax_code);
        $entity->setCorporate($this->corporate);
        $entity->setAddress($this->address);
        $entity->setContactPhone($this->contact_phone);
        $entity->setOwnership(new Ownership($this->ownership,\Map::getStatusName('ownership', $this->ownership)));
        if(!empty($this->build_date)){
            $entity->setBuildDate(new DateTime($this->build_date));
        }
        $entity->setRemark($this->remark);
        $entity->setStatus(new Status($this->status,\DateUtility::getDateTime(), \Map::getStatusName('oil_company_status',$this->status)));

        if(\Utility::isNotEmpty($this->files)){
            foreach($this->files as & $fileDto){
                $fileEntity = $fileDto->toEntity();
                $entity->addFile($fileEntity);
            }
        }

        return $entity;
    }

    /**
     * @param $attribute
     * @return bool
     * @throws \Exception
     */
    public function validateName($attribute):bool {
        $entity = OilCompanyService::service()->getOilCompanyRepository()->find('t.name =:name',[':name'=>$this->name]);
        if(empty($entity) || (!empty($entity) && $this->company_id == $entity->getId())){
            return true;
        }

        $this->addError($attribute,"企业名称已被占用！");
        return false;
    }

    /**
     * @param $attribute
     * @return bool
     * @throws \Exception
     */
    public function validateTaxCode($attribute){
        $entity = OilCompanyService::service()->getOilCompanyRepository()->find('t.tax_code =:tax_code',[':tax_code'=>$this->tax_code]);
        if(empty($entity) || (!empty($entity) && $this->company_id == $entity->getId())){
            return true;
        }

        $this->addError($attribute,"纳税人识别号已被占用！");
        return false;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function validateFiles($attribute){
        foreach($this->files as $fileDto){
            if(OilCompanyFileEnum::TYPE_CERTIFICATE == $fileDto->type){
                return true;
            }
        }

        $this->addError($attribute,'请上传企业证件附件！');
        return false;
    }
}