<?php

namespace ddd\OilStation\Domain\OilStation;


use ddd\Common\Domain\BaseService;

class OilStationService extends BaseService{

    use TraitOilStationRepository;

    /**
     * 批量设置站点为禁用
     * @param int $companyId
     * @throws \Exception
     */
    public function bathSetStationIsUnableByCompanyId(int $companyId):void{
        $entities =  $this->getOilStationRepository()->getAllStationByCompanyId($companyId);
        if(\CheckUtility::isEmpty($entities)){
            return;
        }

        foreach($entities as $entity){
            $this->setStationUnable($entity);
        }
    }

    protected function setStationUnable(OilStation $entity){
        $entity->setOnOff(false);
        \AMQPService::publishOilStationToFinanceSystem($entity->getId());
    }
}