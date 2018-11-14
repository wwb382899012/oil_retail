<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/11 0011
 * Time: 10:37
 */

namespace ddd\Order\Domain\OrderPayment;


use ddd\Common\Domain\BaseEvent;

class OrderPaidEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '订单支付之后';
    }
}