<?php

namespace ddd\OilStation\Domain\OilStation;


use ddd\Common\Domain\BaseService;
use ddd\OilStation\Domain\OilStation\Event\OilStationApplyBackedEvent;
use ddd\OilStation\Domain\OilStation\Event\OilStationApplyPassedEvent;
use ddd\OilStation\Domain\OilStation\Event\OilStationApplySubmittedEvent;

class OilStationApplyService extends BaseService{

    use TraitOilStationApplyRepository;
    use TraitOilStationRepository;

    /**
     * @param OilStationApplySubmittedEvent $event
     * @throws \Exception
     */
    public function onOilStationApplySubmitted(OilStationApplySubmittedEvent $event){
        $this->afterSubmitted($event->sender);
    }

    public function onOilStationApplyBacked(OilStationApplyBackedEvent $event){
        $this->afterBacked($event->sender);
    }

    public function onOilStationApplyPassed(OilStationApplyPassedEvent $event){
        $this->afterPassed($event->sender);
    }

    /**
     * @param \ddd\OilStation\Domain\OilStation\OilStationApply $entity
     * @throws \Exception
     */
    public function afterSubmitted(OilStationApply $entity){
        //TODO: 先直接审核通过
        $entity->setIsPassed($entity);
    }

    public function afterBacked(OilStationApply $entity){

    }

    /**
     * @param OilStationApply $entity
     * @throws \Exception
     */
    public function afterPassed(OilStationApply $entity){
        //获取最新的，否则文件信息不全
        $entity = $this->getOilStationApplyRepository()->findById($entity->getId());

        //复制油站申请数据
        $stationId = $this->getOilStationRepository()->copyByApplyEntity($entity);

        //推送油站信息给财务系统
        \AMQPService::publishOilStationToFinanceSystem($stationId);
    }
}