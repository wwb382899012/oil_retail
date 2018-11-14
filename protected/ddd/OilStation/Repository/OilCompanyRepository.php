<?php


namespace ddd\OilStation\Repository;


use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\OilStation\Domain\OilCompany\IOilCompanyRepository;
use ddd\OilStation\Domain\OilCompany\OilCompany;
use ddd\OilStation\Domain\OilCompany\OilCompanyStatusEnum;
use ddd\OilStation\Domain\Value\Ownership;

class OilCompanyRepository extends OilRepository implements IOilCompanyRepository{

    public function init(){
        $this->with = ['files', 'createUser','updateUser'];
    }

    /**
     * 获取新的实体对象
     * @return BaseEntity|OilCompany
     */
    public function getNewEntity(){
        return new OilCompany();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
        return \OilCompany::class;
    }

    public function dataToEntity($model){
        return $this->toEntity($model);
    }

    public function toEntity(\OilCompany $model):OilCompany{
        $entity = $this->getNewEntity();
        $entity->setId($model->company_id);
        $entity->setName($model->name);
        $entity->setShortName($model->short_name);
        $entity->setTaxCode($model->tax_code);
        $entity->setCorporate($model->corporate);
        $entity->setAddress($model->address);
        $entity->setContactPhone($model->contact_phone);
        $entity->setOwnership(new Ownership($model->ownership, \Map::getStatusName('ownership', $model->ownership)));
        if('0000-00-00' != $model->build_date){
            $entity->setBuildDate(new DateTime($model->build_date));
        }
        $entity->setStatus(new Status($model->status, $model->status_time, \Map::getStatusName('oil_company_status', $model->status)));
        $entity->setFiles($this->getFileEntities($model));
        $this->setCommonAttributes($entity,$model);

        return $entity;
    }

    public function store($entity){
        $this->save($entity);
    }

    /**
     * @param OilCompany $entity
     * @return OilCompany
     * @throws \Exception
     */
    public function save(OilCompany $entity){
        if(!empty($entity->getId())){
            $model = \OilCompany::model()->findByPk($entity->getId());
            if(empty($model)){
                throw new ZModelNotExistsException($entity->getId(), \OilCompany::class);
            }
        }else{
            $model = new \OilCompany();
        }

        $model->setAttributes($entity->getAttributes());
        $model->name = $entity->getName();
        $model->short_name = $entity->getShortName();
        $model->tax_code = $entity->getTaxCode();
        $model->corporate = $entity->getCorporate();
        $model->address = $entity->getAddress();
        $model->contact_phone = $entity->getContactPhone();
        $model->ownership = $entity->getOwnershipValue();
        $model->build_date = empty($entity->getBuildDate()) ? '' : $entity->getBuildDate()->toDate();
        $model->remark = $entity->getRemark();
        $model->status = $entity->getStatusValue();
        $model->status_time = $entity->getStatusTime();
        $model->effect_time = $entity->isActive() ? \DateUtility::getDateTime() : '';
        if(!$model->save()){
            throw new ZModelSaveFalseException($model);
        }

        $entity->setId($model->getPrimaryKey());

        //保存附件
        $this->saveFiles(\OilCompanyAttachment::class, $model, $entity->getFiles());

        return $entity;
    }

    /**
     * @param $id
     * @return OilCompany|null
     * @throws \Exception
     */
    public function findById($id):?OilCompany{
        return parent::findById($id);
    }


    /**
     * @param string $condition
     * @param array  $params
     * @return OilCompany|null
     * @throws \Exception
     */
    public function find($condition = '', $params = array()):?OilCompany{
        return parent::find($condition, $params);
    }

    /**
     * 获取所有有效企业id名称
     * @return array
     * @throws \CDbException
     * @throws \CException
     */
    public function getAllActiveCompanyIdNames():array{
        $data = $this->model()->findAll([
            'select'    => 'company_id,name',
            'condition' => 't.status = :status',
            'params' => [':status' => OilCompanyStatusEnum::ENABLE],
        ]);
        if(empty($data)){
            return [];
        }

        return array_column($data,'name','company_id');
    }

    /**
     * 获取所有企业id名称
     * @return array
     * @throws \CDbException
     * @throws \CException
     */
    public function getAllCompanyIdNames():array{
        $data = $this->model()->findAll([
            'select'    => 'company_id,name',
        ]);
        if(empty($data)){
            return [];
        }

        return array_column($data,'name','company_id');
    }
}