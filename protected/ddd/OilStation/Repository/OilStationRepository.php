<?php


namespace ddd\OilStation\Repository;


use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\OilStation\Domain\Attachment;
use ddd\OilStation\Domain\OilStation\IOilStationRepository;
use ddd\OilStation\Domain\OilStation\OilStation;
use ddd\OilStation\Domain\OilStation\OilStationApply;
use ddd\OilStation\Domain\OilStation\OilStationEnum;
use ddd\OilStation\Domain\Value\Area;
use ddd\OilStation\Domain\Value\Company;
use ddd\OilStation\Domain\Value\Contact;
use ddd\OilStation\Domain\Value\Position;

class OilStationRepository extends OilRepository implements IOilStationRepository{

    public function init(){
        $this->with = ['company','province','city','files','createUser','updateUser'];
    }

    /**
     * 获取新的实体对象
     * @return BaseEntity|OilStation
     */
    public function getNewEntity(){
        return new OilStation();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
        return \OilStation::class;
    }

    public function dataToEntity($model){
        return $this->toEntity($model);
    }

    /**
     * @param \OilStation $model
     * @return OilStation
     */
    public function toEntity(\OilStation $model):OilStation{
        $entity = new OilStation();
        $entity->setId($model->apply_id);
        $entity->setName($model->name);
        $entity->setCompany(new Company($model->company_id,$model->company->name));
        $entity->setProvince(new Area($model->province_id,$model->province->area_name));
        $entity->setCity(new Area($model->city_id,$model->city->area_name));
        $entity->setAddress($model->address);
        $entity->setPosition(new Position($model->longitude,$model->latitude));
        $entity->setContact(new Contact($model->contact_person,$model->contact_phone));
        $entity->setStatus(new Status($model->status,'', \Map::getStatusName('oil_station_status',$model->status)));
        $entity->setFiles($this->getFileEntities($model));
        $this->setCommonAttributes($entity,$model);

        return $entity;
    }

    /**
     * @param $entity
     * @return OilStation
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
     * @param OilStation $entity
     * @return OilStation
     * @throws ZModelNotExistsException
     * @throws ZModelSaveFalseException
     * @throws \CDbException
     * @throws \CException
     * @throws \ddd\Infrastructure\error\ZModelDeleteFalseException
     */
    public function save(OilStation $entity):OilStation{
        if(!empty($entity->getId())){
            $model = \OilStation::model()->findByPk($entity->getId());
            if(empty($model)){
                throw new ZModelNotExistsException($entity->getId(),\OilStation::class);
            }
        }else{
            $model = new \OilStation();
        }

        $model->setAttributes($entity->getAttributes());
        $model->name  = $entity->getName();
        $model->company_id = $entity->getCompanyId();
        $model->province_id = $entity->getProvinceId();
        $model->city_id = $entity->getCityId();
        $model->address = $entity->getAddress();
        $model->longitude = $entity->getLongitude();
        $model->latitude = $entity->getLatitude();
        $model->contact_person = $entity->getContactName();
        $model->contact_phone = $entity->getContactPhone();
        $model->remark = $entity->getRemark();
        $model->status = $entity->getStatusValue();

        if(!$model->save()){
            throw new ZModelSaveFalseException($model);
        }

        $entity->setId($model->getPrimaryKey());

        //保存附件
        $this->saveFiles(\OilStationAttachment::class,$model,$entity->getFiles());

        return $entity;
    }

    /**
     * 复制油站申请数据
     * @param OilStationApply $entity
     * @return int
     * @throws ZModelSaveFalseException
     * @throws \CDbException
     */
    public function copyByApplyEntity(OilStationApply $entity):int {
        $model = new \OilStation();
        $model->station_id = $entity->getId();
        $model->apply_id = $entity->getId();
        $model->name = $entity->getName();
        $model->company_id = $entity->getCompanyId();
        $model->province_id = $entity->getProvinceId();
        $model->city_id = $entity->getCityId();
        $model->address = $entity->getAddress();
        $model->longitude = (float)$entity->getLongitude();
        $model->latitude = (float)$entity->getLatitude();
        $model->contact_person = $entity->getContactName();
        $model->contact_phone = $entity->getContactPhone();
        $model->remark = $entity->getRemark();
        $model->status = OilStationEnum::ENABLE;
        $model->status_time = \DateUtility::getDateTime();
        $model->effect_time = \DateUtility::getDateTime();

        if(!$model->save()){
            throw new ZModelSaveFalseException($model);
        }

        //保存附件
        $files = $entity->getFiles();
        if(\CheckUtility::isNotEmpty($files)){
            foreach($files as & $fileEntity){
                $this->copyByApplyFile($model->getPrimaryKey(),$fileEntity);
            }
        }

        return $model->getPrimaryKey();
    }

    /**
     * @param            $baseId
     * @param Attachment $entity
     * @throws ZModelSaveFalseException
     * @throws \CDbException
     */
    protected function copyByApplyFile($baseId,Attachment $entity):void {
        $fileModel = new \OilStationAttachment();
        $fileModel->base_id = $baseId;
        $fileModel->type = $entity->getType();
        $fileModel->name = $entity->getName();
        $fileModel->file_path = $entity->getPath();
        $fileModel->file_url = $entity->getUrl();
        $fileModel->status = $entity->getStatus();
        $fileModel->remark = $entity->getRemark();

        if(!$fileModel->save()){
            throw new ZModelSaveFalseException($fileModel);
        }
    }

    /**
     * 更新油站状态
     * @param OilStation $entity
     * @throws \CDbException
     * @throws \CException
     */
    public function updateStatus(OilStation $entity):void {
        \OilStation::model()->updateByPk($entity->getId(),[
            'status'=> $entity->getStatusValue(),
            'status_time'=> $entity->getStatusTime(),
        ]);
    }

    /**
     * @param $id
     * @return OilStation|null
     * @throws \Exception
     */
    public function findById($id):?OilStation{
        return parent::findById($id);
    }

    /**
     * @param string $condition
     * @param array  $params
     * @return OilStation|null
     * @throws \Exception
     */
    public function find($condition = '', $params = array()):?OilStation{
        return parent::find($condition, $params);
    }

    /**
     * @return array
     * @throws \CDbException
     * @throws \CException
     */
    public function getAllActiveStationIdNames():array{
        $data = $this->model()->findAll([
            'select'    => 'station_id,name',
            'condition' => 't.status = :status',
            'params' => [':status' => OilStationEnum::ENABLE],
        ]);
        if(empty($data)){
            return [];
        }

        return array_column($data,'name','station_id');
    }

    /**
     * 获取某公司下所有的站点
     * @param int $companyId
     * @return array
     * @throws \Exception
     */
    function getAllStationByCompanyId(int $companyId):array{
        $entities = $this->findAll('t.company_id = :company_id',[':company_id'=> $companyId]);

        return empty($entities) ? [] : $entities;
    }

}