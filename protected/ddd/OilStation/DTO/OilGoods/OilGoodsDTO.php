<?php

namespace ddd\OilStation\DTO\OilGoods;


use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\OilStation\Application\OilGoodsService;
use ddd\OilStation\Domain\OilGoods\OilGoods;
use ddd\OilStation\DTO\OilCommonDto;

class OilGoodsDTO extends OilCommonDto{
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
    public $name = '';

    /**
     * 排序
     * @var   int
     */
    public $sort = 0;


    #endregion

    public function rules(){
        return [
            ["name", "required", "message" => "请填写商品名称"],
            ["files",'validateName'],
        ];
    }

    public function fromEntity(BaseEntity $entity):void {
        $this->entityToDto($entity);
    }

    private function entityToDto(OilGoods $entity){
        $this->goods_id = $entity->getId();
        $this->name = $entity->getName();
        $this->sort = $entity->getSort();
        $this->sort = 0 == $this->sort ? '' : $this->sort;

        parent::fromEntity($entity);
    }

    public function toEntity():OilGoods{
        $entity = new OilGoods();
        $entity->setId($this->goods_id);
        $entity->setName($this->name);
        $entity->setSort($this->sort);
        $entity->setRemark($this->remark);
        $entity->setStatus(new Status($this->status,\DateUtility::getDateTime(),\Map::getStatusName('oil_goods_status',$this->status)));

        return $entity;
    }

    public function validateName($attribute):bool {
        $entity = OilGoodsService::service()->getOilGoodsRepository()->find('t.name =:name',[':name'=>$this->name]);
        if(empty($entity) || (!empty($entity) && $this->goods_id == $entity->getId())){
            return true;
        }

        $this->addError($attribute,"油品名称已被占用！");
        return false;
    }
}