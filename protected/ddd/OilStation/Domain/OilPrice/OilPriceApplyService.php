<?php

namespace ddd\OilStation\Domain\OilPrice;


use app\ddd\OilStation\Domain\OilPrice\PriceEffectService;
use ddd\Common\Domain\BaseService;
use ddd\OilStation\Domain\OilPrice\Event\OilPriceApplyBackedEvent;
use ddd\OilStation\Domain\OilPrice\Event\OilPriceApplyPassedEvent;
use ddd\OilStation\Domain\OilPrice\Event\OilPriceApplySubmittedEvent;

class OilPriceApplyService extends BaseService{

    public function onOilPriceApplySubmitted(OilPriceApplySubmittedEvent $event){
        $this->afterSubmitted($event->sender);
    }

    public function onOilPriceApplyBacked(OilPriceApplyBackedEvent $event){
        $this->afterPassed($event->sender);
    }

    public function onOilPriceApplyPassed(OilPriceApplyPassedEvent $event){
        $this->afterPassed($event->sender);
    }

    /**
     * @param OilPriceApply $entity
     * @throws \Exception
     */
    protected function afterSubmitted(OilPriceApply $entity):void {
        //TODO: 先直接审核通过
        $entity->checkPass();
    }

    protected function afterBacked(OilPriceApply $entity):void {

    }

    /**
     * @param OilPriceApply $entity
     * @throws \Exception
     */
    protected function afterPassed(OilPriceApply $entity):void {
        //价格申请生效
        PriceEffectService::service()->effect($entity);
    }
}