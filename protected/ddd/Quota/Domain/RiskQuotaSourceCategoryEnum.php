<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/6 0006
 * Time: 10:30
 */

namespace ddd\Quota\Domain;


class RiskQuotaSourceCategoryEnum
{
    /**
     * 订单
     */
    const Order_Payment = 10; //订单支付

    /**
     * 还款
     */
    const Logistics_Repay = 20; //物流还款
}