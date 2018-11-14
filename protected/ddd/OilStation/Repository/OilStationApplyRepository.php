<?php


namespace ddd\OilStation\Repository;


use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\OilStation\Domain\OilStation\IOilStationApplyRepository;
use ddd\OilStation\Domain\OilStation\OilStationApply;
use ddd\OilStation\Domain\Value\Area;
use ddd\OilStation\Domain\Value\Company;
use ddd\OilStation\Domain\Value\Contact;
use ddd\OilStation\Domain\Value\Position;

class OilStationApplyRepository extends OilRepository implements IOilStationApplyRepository{

    public function init(){
        $this->with = ['company','province','city','files','createUser','updateUser'];
    }

    /**
     * 获取新的实体对象
     * @return BaseEntity|OilStationApply
     */
    public function getNewEntity(){
        return new OilStationApply();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
        return \OilStationApply::class;
    }

    public function dataToEntity($model){
        return $this->toEntity($model);
    }

    /**
     * @param \OilStationApply $model
     * @return OilStationApply
     */
    public function toEntity(\OilStationApply $model):OilStationApply{
        $entity = new OilStationApply();
        $entity->setId($model->apply_id);
        $entity->setName($model->name);
        $entity->setCompany(new Company($model->company_id,$model->company->name));
        $entity->setProvince(new Area($model->province_id,$model->province->area_name));
        $entity->setCity(new Area($model->city_id,$model->city->area_name));
        $entity->setAddress($model->address);
        $entity->setPosition(new Position($model->longitude,$model->latitude));
        $entity->setContact(new Contact($model->contact_person,$model->contact_phone));
        $entity->setRemark($model->remark);
        $entity->setStatus(new Status($model->status,'', \Map::getStatusName('oil_station_apply_status',$model->status)));
        $entity->setFiles($this->getFileEntities($model));
        $this->setCommonAttributes($entity,$model);

        return $entity;
    }

    /**
     * @param $entity
     * @return OilStationApply
     * @throws ZModelNotExistsException
     * @throws ZModelSaveFalseException
     * @throws \CDbException
     * @throws \CException
     * @throws \ddd\Infrastructure\error\ZModelDeleteFalseException
     */
    public function store($entity){
        return $this->save($entity);
    }

    /**
     * @param OilStationApply $entity
     * @return OilStationApply
     * @throws ZModelNotExistsException
     * @throws ZModelSaveFalseException
     * @throws \CDbException
     * @throws \CException
     * @throws \ddd\Infrastructure\error\ZModelDeleteFalseException
     */
    public function save(OilStationApply $entity):OilStationApply{
        if(!empty($entity->getId())){
            $model = \OilStationApply::model()->findByPk($entity->getId());
            if(empty($model)){
                throw new ZModelNotExistsException($entity->getId(),\OilStationApply::class);
            }
        }else{
            $model = new \OilStationApply();
        }

        $model->setAttributes($entity->getAttributes());
        $model->name  = $entity->getName();
        $model->company_id = $entity->getCompanyId();
        $model->province_id = $entity->getProvinceId();
        $model->city_id = $entity->getCityId();
        $model->address = $entity->getAddress();
        $model->longitude = empty($entity->getLongitude()) ? 0 : $entity->getLongitude();
        $model->latitude = empty($entity->getLatitude()) ? 0 : $entity->getLatitude();
        $model->contact_person = $entity->getContactName();
        $model->contact_phone = $entity->getContactPhone();
        $model->remark = $entity->getRemark();
        $model->status = $entity->getStatusValue();
        $model->effect_time = \DateUtility::getDateTime();

        if(!$model->save()){
            throw new ZModelSaveFalseException($model);
        }

        $entity->setId($model->getPrimaryKey());

        //保存附件
        $this->saveFiles(\OilStationApplyAttachment::class,$model,$entity->getFiles());

        return $entity;
    }

    public function updateStatus(OilStationApply $entity):void {
        $this->model()->updateByPk($entity->getId(),[
            'status'=> $entity->getStatusValue(),
            'status_time'=> $entity->getStatusTime()
        ]);
    }

    /**
     * @param $id
     * @return OilStationApply|null
     * @throws \Exception
     */
    public function findById($id):?OilStationApply{
        return parent::findById($id);
    }

    /**
     * @param string $condition
     * @param array  $params
     * @return OilStationApply|null
     * @throws \CException
     */
    public function find($condition = '', $params = array()):?OilStationApply{
        return parent::find($condition, $params);
    }
}