<?php


namespace ddd\OilStation\Repository;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Repository\EntityRepository;
use ddd\OilStation\Domain\Attachment;
use ddd\OilStation\Domain\OilPrice\IOilPriceApplyAttachmentRepository;
use ddd\OilStation\Domain\OilStation\OilStation;

class OilPriceApplyAttachmentRepository extends EntityRepository implements IOilPriceApplyAttachmentRepository{

    public function init(){
        $this->with = [];
    }

    /**
     * 获取新的实体对象
     * @return BaseEntity|OilStation
     */
    public function getNewEntity(){
        return new Attachment();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
        return \OilPriceApplyAttachment::class;
    }

    public function dataToEntity($model){
        return $this->toEntity($model);
    }

    /**
     * @param \OilPriceApplyAttachment $model
     * @return Attachment
     */
    protected function toEntity(\OilPriceApplyAttachment $model):Attachment{
        $entity =  new Attachment();
        $entity->setId($model->id);
        $entity->setType($model->type);
        $entity->setName($model->name);
        $entity->setUrl($model->file_url);
        $entity->setPath($model->file_path);
        $entity->setRemark($model->remark);
        $entity->setStatus($model->status);

        return $entity;
    }

    /**
     * @param $entity
     * @return mixed
     */
    public function store($entity){
        return $entity;
    }

    /**
     * @param $id
     * @return Attachment|null
     * @throws \Exception
     */
    public function findById($id):?Attachment{
        return parent::findById($id);
    }

    /**
     * @param string $condition
     * @param array  $params
     * @return Attachment|null
     * @throws \CException
     */
    public function find($condition = '', $params = array()):?Attachment{
        return parent::find($condition, $params);
    }

}