<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/11 0011
 * Time: 10:23
 */

namespace ddd\Order\Domain\OrderPayment;


class OrderPaymentStatusEnum
{
    const Status_New = 0; //新建，待支付

    const Status_In_Payment = 5; //支付中

    const Status_Done = 10;//付款完成
}