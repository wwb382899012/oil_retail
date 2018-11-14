<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/7 0007
 * Time: 18:55
 */

namespace ddd\Order\Domain\Order;


use ddd\Common\Domain\BaseEvent;

class OrderEffectedEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '订单生效';
    }
}