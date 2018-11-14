<?php
/**
 * Desc: 物流企业额度变化记录基类
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 16:38
 */

namespace ddd\Quota\Domain\LogisticsQuota;


use ddd\Quota\Domain\BaseRiskQuotaLog;

class BaseLogisticsQuotaLog extends BaseRiskQuotaLog
{

    /**
     * 物流企业id
     * @var   int
     */
    public $logistics_id;

    public function getQuotaObjectPropertyName()
    {
        return "logistics_id";
    }
}