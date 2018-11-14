<?php

namespace ddd\OilStation\Domain\OilPrice\Event;

use ddd\Common\Domain\BaseEvent;

class OilPriceApplyBackedEvent extends BaseEvent{
    function initEventName(){
        parent::initEventName();
        $this->eventName = '油品价格审核驳回';
    }
}