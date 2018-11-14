<?php


namespace ddd\OilStation\Repository;


use ddd\Common\Domain\BaseEntity;
use ddd\OilStation\Domain\OilPhone\IOilPhoneRepository;
use ddd\OilStation\Domain\OilPhone\OilPhone;

class OilPhoneRepository extends OilRepository implements IOilPhoneRepository{

    public function init(){
        $this->with = [];
    }

    /**
     * 获取新的实体对象
     * @return BaseEntity|OilPhone
     */
    public function getNewEntity(){
        return new OilPhone();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
        return \OilPhone::class;
    }

    public function dataToEntity($model){
        return parent::dataToEntity($model);
    }

    public function store($entity){
        return parent::store($entity);
    }

    /**
     * @param $id
     * @return OilPhone|null
     * @throws \Exception
     */
    public function findById($id):?OilPhone{
        return parent::findById($id);
    }

    /**
     * @param string $condition
     * @param array  $params
     * @return OilPhone|null
     * @throws \CException
     */
    public function find($condition = '', $params = array()):?OilPhone{
        return parent::find($condition, $params);
    }
}