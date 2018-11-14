<?php
/**
 * User: liyu
 * Date: 2018/9/5
 * Time: 15:02
 * Desc: 油品
 */

namespace ddd\OilStation\Domain\OilGoods;


use ddd\OilStation\Domain\OilCommonEntity;

class OilGoods extends OilCommonEntity{

    use TraitOilGoodsRepository;

    #region property

    /**
     * 标识
     * @var   int
     */
    public $goods_id = 0;

    /**
     * 名称
     * @var   string
     */
    public $name;

    /**
     * 排序
     * @var   int
     */
    public $sort;

    #endregion

    #region get set methods

    /**
     * @return int
     */
    public function getId():int{
        return $this->goods_id;
    }

    /**
     * @param int $goods_id
     */
    public function setId(int $goods_id):void{
        $this->goods_id = $goods_id;
    }

    /**
     * @return string
     */
    public function getName():string{
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name):void{
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getSort():int{
        return $this->sort;
    }

    /**
     * @param int $sort
     */
    public function setSort(int $sort):void{
        $this->sort = $sort;
    }

    #endregion

    #region get ext methods


    #endregion

    #region logic methods

    /**
     * 创建
     * @return   OilGoods
     */
    public static function create():OilGoods{
        // TODO: implement
    }

    /**
     * 是否可用油品
     * @return   boolean
     */
    public function isActive(){
        return $this->getStatusValue() == OilGoodsEnum::ENABLE;
    }

    /**
     * @param bool $persistent
     * @throws \ddd\Infrastructure\error\ZModelNotExistsException
     * @throws \ddd\Infrastructure\error\ZModelSaveFalseException
     */
    public function save($persistent=true){
        if($persistent){
            $this->getOilGoodsRepository()->store($this);
        }
    }

    #endregion

}