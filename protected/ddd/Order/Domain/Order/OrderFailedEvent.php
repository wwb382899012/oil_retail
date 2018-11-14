<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/11 0011
 * Time: 14:51
 */

namespace ddd\Order\Domain\Order;


use ddd\Common\Domain\BaseEvent;

class OrderFailedEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '订单失败';
    }
}