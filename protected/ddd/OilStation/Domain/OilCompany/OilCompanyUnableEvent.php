<?php

namespace ddd\OilStation\Domain\OilCompany;

use ddd\Common\Domain\BaseEvent;

class OilCompanyUnableEvent extends BaseEvent{
    function initEventName(){
        parent::initEventName();
        $this->eventName = '油企禁用';
    }
}