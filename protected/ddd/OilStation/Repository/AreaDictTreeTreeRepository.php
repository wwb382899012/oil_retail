<?php


namespace ddd\OilStation\Repository;


use app\ddd\Common\Repository\RedisCache;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Repository\EntityRepository;
use ddd\OilStation\Domain\AreaDictTree;
use ddd\OilStation\Domain\IAreaDictTreeRepository;

class AreaDictTreeTreeRepository extends EntityRepository implements IAreaDictTreeRepository{

    use RedisCache;

    const AREA_DICT_CACHE_KEY = ':area_dict';

    /**
     * 获取新的实体对象
     * @return BaseEntity
     */
    public function getNewEntity(){
        return new AreaDictTree();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
       return \AreaCode::class;
    }

    function findById($id){
        return null;
    }

    function store($entity){
        return $entity;
    }

    function getTree():AreaDictTree{
        $tree = $this->getEntityFromCache(self::AREA_DICT_CACHE_KEY);
        if(empty($tree)){
            $condition = ["order"=>"t.level ASC,t.area_code ASC","condition"=> 't.area_code <> 0 AND t.level < 3'];
            $items = \AreaCode::model()->findAll($condition);
            if(\CheckUtility::isEmpty($items)){
                return new AreaDictTree();
            }

            $tree= new AreaDictTree();
            $tree->setId(0);
            $tree->setName( '中国');

            $this->generateChildren($tree,$items);

            $this->setCache(self::AREA_DICT_CACHE_KEY,$tree);
        }

        return $tree;
    }

    /**
     * @param AreaDictTree $tree
     * @param array        $items
     * @return AreaDictTree
     */
    protected function generateChildren(AreaDictTree &$tree, array & $items){
        foreach ($items as $k => & $item){
            if($item->p_area_code == $tree->getId()){
                unset($items[$k]);
                $child = $this->modelToEntity($item);
                $tree->addChild($child);
                $this->generateChildren($child,$items);
            }
        }
        return $tree;
    }

    /**
     * 根据model获取实体
     * @param \AreaCode $model
     * @return AreaDictTree
     */
    protected function modelToEntity(\AreaCode $model){
        $tree=new AreaDictTree();
        $tree->setId($model->area_code);
        $tree->setName($model->area_name);
        return $tree;
    }
}