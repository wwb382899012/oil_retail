<?php

namespace ddd\OilStation\Domain\OilPrice\Event;

use ddd\Common\Domain\BaseEvent;

class OilPriceApplySubmittedEvent extends BaseEvent{
    function initEventName(){
        parent::initEventName();
        $this->eventName = '油品价格提交';
    }
}