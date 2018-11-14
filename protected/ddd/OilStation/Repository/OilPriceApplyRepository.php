<?php


namespace ddd\OilStation\Repository;

use ddd\Common\Domain\BaseEntity;
use ddd\Infrastructure\error\ZException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\OilStation\Domain\OilPrice\IOilPriceApplyRepository;
use ddd\OilStation\Domain\OilPrice\OilPriceApply;
use ddd\OilStation\Domain\OilPrice\OilPriceItem;

class OilPriceApplyRepository extends OilRepository implements IOilPriceApplyRepository{

    public function init(){
        $this->with = ['createUser','updateUser'];
    }

    /**
     * 获取新的实体对象
     * @return BaseEntity|OilPriceApply
     */
    public function getNewEntity(){
        return new OilPriceApply();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
        return \OilPriceApply::class;
    }

    public function dataToEntity($model){
        return parent::dataToEntity($model);
    }

    /**
     * @param $entity
     * @throws \Exception
     */
    public function store($entity){
        $this->save($entity);
    }

    /**
     * @param OilPriceApply $entity
     * @return OilPriceApply
     * @throws \Exception
     */
    public function save(OilPriceApply $entity):OilPriceApply{
        $model = \OilPriceApply::model()->find('t.code = :code',[':code'=> $entity->getCode()]);
        if(empty($model)){
            $model = new \OilPriceApply();
        }
        $model->code = $entity->getCode();
        $model->remark = $entity->getRemark();
        $model->status = $entity->getStatusValue();
        $model->status_time = $entity->getStatusTime();
        $model->effect_time = $entity->getEffectTime()->toDateTime();

        if(!$model->save()){
            throw new ZModelSaveFalseException($model);
        }
        $entity->setId($model->getPrimaryKey());

        $itemEntities = $entity->getItems();
        if(\CheckUtility::isNotEmpty($itemEntities)){
            foreach($itemEntities as & $itemEntity){
                $this->saveDetail($model,$itemEntity);
            }
        }

        return $entity;
    }

    /**
     * @param \OilPriceApply $model
     * @param OilPriceItem   $entity
     * @throws \Exception
     */
    protected function saveDetail(\OilPriceApply & $model,OilPriceItem & $entity):void {
        $detailModel = new \OilPriceItem();
        $detailModel->setAttributes($model->getAttributes());
        $detailModel->item_id = null;
        $detailModel->company_id = $entity->getCompanyId();
        $detailModel->station_id = $entity->getStationId();
        $detailModel->goods_id = $entity->getGoodsId();
        $detailModel->retail_price = $entity->getRetailPrice();
        $detailModel->agreed_price = $entity->getAgreedPrice();
        $detailModel->discount_price = $entity->getDiscountPrice();
        $detailModel->remark = $entity->getRemark();

        if(!$detailModel->save()){
            throw new ZModelSaveFalseException($detailModel);
        }
        $entity->setItemId($detailModel->getPrimaryKey());
    }

    /**
     * @param OilPriceApply $entity
     * @throws \Exception
     */
    public function updateStatus(OilPriceApply $entity):void {
        $this->model()->updateByPk($entity->getId(),[
            'status'=> $entity->getStatusValue(),
            'status_time'=> $entity->getStatusTime()
        ]);
    }

    /**
     * @param $id
     * @return OilPriceApply|null
     * @throws \Exception
     */
    public function findById($id):?OilPriceApply{
        return parent::findById($id);
    }

    /**
     * @param string $condition
     * @param array  $params
     * @return OilPriceApply|null
     * @throws \CException
     */
    public function find($condition = '', $params = array()):?OilPriceApply{
        return parent::find($condition, $params);
    }
}