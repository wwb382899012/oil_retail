<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/7 0007
 * Time: 18:39
 */

namespace ddd\Order\Domain\Order;


class OrderStatusEnum
{
    const Status_Failed = - 1; //失败

    const Status_New = 0; //保存

    const Status_Effected = 10; //已生效
}