<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/11 0011
 * Time: 11:06
 */

namespace ddd\Order\Domain\OrderPayment;


use ddd\Common\Domain\BaseEvent;

class OrderPaymentEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '订单支付';
    }
}