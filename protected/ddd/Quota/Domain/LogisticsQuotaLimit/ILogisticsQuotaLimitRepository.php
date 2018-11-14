<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/4 0004
 * Time: 16:05
 */

namespace ddd\Quota\Domain\LogisticsQuotaLimit;


use ddd\Common\Domain\IRepository;

interface ILogisticsQuotaLimitRepository extends IRepository
{
    /**
     * 获取物流企业当前可用限额设置
     * @return   LogisticsQuotaLimit
     */
    public function getActiveLogisticsQuotaLimit();
}