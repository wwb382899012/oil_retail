<?php


namespace ddd\OilStation\Repository;


use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\OilStation\Domain\OilGoods\IOilGoodsRepository;
use ddd\OilStation\Domain\OilGoods\OilGoods;
use ddd\OilStation\Domain\OilGoods\OilGoodsEnum;

class OilGoodsRepository extends OilRepository implements IOilGoodsRepository {

    public function init(){
        $this->with = ['createUser','updateUser'];
    }

    /**
     * 获取新的实体对象
     * @return BaseEntity|OilGoods
     */
    public function getNewEntity(){
        return new OilGoods();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
        return \OilGoods::class;
    }

    public function dataToEntity($model):OilGoods{
        return $this->toEntity($model);
    }

    public function toEntity(\OilGoods $model){
        $entity = new OilGoods();
        $entity->setId($model->goods_id);
        $entity->setName($model->name);
        $entity->setSort($model->order_index);
        $entity->setStatus(new Status($model->status,$model->status_time,\Map::getStatusName('oil_goods_status',$model->status)));
        $this->setCommonAttributes($entity,$model);

        return $entity;
    }

    public function store($entity):OilGoods{
       return $this->save($entity);
    }

    public function save(OilGoods $entity):OilGoods{
        if(!empty($entity->getId())){
            $model = \OilGoods::model()->findByPk($entity->getId());
            if(empty($model)){
                throw new ZModelNotExistsException($entity->getId(),\OilGoods::class);
            }
        }else{
            $model = new \OilGoods();
        }

        $model->name = $entity->getName();
        $model->code = '';
        $model->order_index = $entity->getSort();
        $model->remark = $entity->getRemark();
        $model->status = $entity->getStatusValue();
        $model->effect_time = $entity->isActive() ? \DateUtility::getDateTime() : '';

        if(!$model->save()){
            throw new ZModelSaveFalseException($model);
        }
        $entity->setId($model->getPrimaryKey());

        return $entity;
    }

    /**
     * @param $id
     * @return OilGoods
     * @throws \Exception
     */
    public function findById($id):?OilGoods{
        return parent::findById($id);
    }

    /**
     * @param string $condition
     * @param array  $params
     * @return OilGoods|null
     * @throws \Exception
     */
    public function find($condition = '', $params = array()):?OilGoods{
        return parent::find($condition, $params);
    }


    public function getAllActiveGoodsIdNames():array {
        $data = $this->model()->findAll([
            'select'    => 'goods_id,name',
            'condition' => 't.status = :status',
            'params' => [':status' => OilGoodsEnum::ENABLE],
        ]);
        if(empty($data)){
            return [];
        }

        return array_column($data,'name','goods_id');
    }
}