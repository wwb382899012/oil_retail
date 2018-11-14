<?php

namespace ddd\OilStation\Domain\OilStation\Event;

use ddd\Common\Domain\BaseEvent;

class OilStationApplyPassedEvent extends BaseEvent{
    function initEventName(){
        parent::initEventName();
        $this->eventName = '油站准入审核通过';
    }
}