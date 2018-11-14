<?php

namespace ddd\OilStation\Domain\OilCompany;


use ddd\Common\Domain\BaseService;
use ddd\OilStation\Domain\OilStation\OilStationService;

class OilCompanyService extends BaseService{

    public function onOilCompanyUnable(OilCompanyUnableEvent $event){
        //目前不做站点、油企关联性处理
        return;

        $this->unableAllStationOfOilCompany($event->sender);
    }

    public function unableAllStationOfOilCompany(OilCompany $entity):void{
        //Todo:后期应该发送通知

        OilStationService::service()->bathSetStationIsUnableByCompanyId($entity->getId());
    }
}