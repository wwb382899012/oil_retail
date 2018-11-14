<?php

namespace ddd\OilStation\DTO;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\OilStation\Domain\OilCommonEntity;

abstract class OilCommonDto extends BaseDTO{

    public $remark = '';

    public $effect_time;

    public $create_time;

    public $update_user_id = 0;

    public $update_user_name = '';

    public $update_time;

    public $create_user_id = 0;

    public $create_user_name = '';

    public $status = 0;

    public $status_name = '';

    public $status_time = '';

    public function fromEntity(BaseEntity $entity):void {
        $this->entityToDto($entity);
    }

    private function entityToDto(OilCommonEntity $entity){
        $this->remark = $entity->getRemark();
        $this->status = $entity->getStatusValue();
        $this->status_name = $entity->getStatusName();
        $this->status_time = $entity->getStatusTime();
        $this->effect_time = $entity->getEffectTime()->toDateTime();
        $this->create_time = $entity->getCreateTime()->toDateTime();
        $this->create_user_id = $entity->getCreateUserId();
        $this->create_user_name = $entity->getCreateUserName();
        $this->update_time = $entity->getUpdateTime()->toDateTime();
        $this->update_user_id = $entity->getUpdateUserId();
        $this->update_user_name = $entity->getUpdateUserName();
    }
}