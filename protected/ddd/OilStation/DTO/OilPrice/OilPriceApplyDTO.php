<?php

namespace ddd\OilStation\DTO\OilPrice;

use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\OilStation\Domain\OilPrice\OilPriceApply;
use ddd\OilStation\DTO\OilFileDto;

class OilPriceApplyDTO extends OilFileDto{

    #region property

    public $apply_id = 0;

    public $code;

    public $items = [];

    #endregion

    public function rules(){
        return [
            ["items",'validateItems'],
        ];
    }

    public function fromEntity(BaseEntity $entity):void {
        $this->entityToDto($entity);
    }

    private function entityToDto(OilPriceApply $entity){
        $this->apply_id = $entity->getId();
        $this->code = $entity->getCode();


        parent::fromEntity($entity);
    }

    public function toEntity():OilPriceApply {
        $entity = new OilPriceApply();
        $entity->setId($this->apply_id);
        $entity->setCode($this->code);
        $entity->setRemark($this->remark);
        $entity->setEffectTime(new DateTime($this->effect_time));
        $entity->setCreateTime(new DateTime($this->create_time));
        $entity->setUpdateTime(new DateTime($this->update_time));
        $entity->setStatus(new Status($this->status,$this->status_time,\Map::getStatusName('oil_apply_status',$this->status)));

        $entity->clearOilPrice();
        if(\CheckUtility::isNotEmpty($this->items)){
            foreach($this->items as $itemDto){
                $itemEntity = $itemDto->toEntity();
                $entity->addOilPrice($itemEntity);
            }
        }

        $entity->clearFiles();
        $this->setFilesToEntity($entity);

        return $entity;
    }

    /**
     * 校验价格
     * @param $attribute
     * @return bool
     */
    public function validateItems($attribute):bool{
        if(empty($this->items)){
            $this->addError($attribute,'请导入价格！');
            return false;
        }

        foreach($this->items as & $itemDto){
            if(!$itemDto->validate()){
                $this->addError($attribute, $itemDto->getErrors());
                return false;
            }
        }

        return true;
    }

}