<?php

/**
 * @Name            物流企业额度
 * @DateTime        2018年9月6日 10:02:47
 * @Author          vector
 */

namespace ddd\Logistics\Domain\LogisticsCompany;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\Money;
use ddd\Common\Domain\Value\DateTime;
use ddd\Infrastructure\Utility;

class LogisticsCompanyCreditQuota extends BaseEntity
{
    #region property

    /**
     * 企业授信额度
     * @var   Money
     */
    public $credit_quota;

    /**
     * 开始日期
     * @var   DateTime
     */
    public $start_date;

    /**
     * 结束日期
     * @var   DateTime
     */
    public $end_date;


    #endregion

    /**
     * 创建
     * @return   LogisticsCompany
     */
    public static function create($creditQuota, $startDate, $endDate)
    {
        $entity = new static();

        if(empty($creditQuota) || empty($startDate) || empty($endDate))
            return $entity;

        $entity->credit_quota = new Money($creditQuota);
        $entity->start_date   = new Datetime($startDate);
        $entity->end_date     = new DateTime($endDate);

        return $entity;
    }

    /**
     * 是否可用
     * @return bool
     */
    public function isActive()
    {
        $date = Utility::getDate();
        return strtotime($this->start_date->toDate()) <= strtotime($date) && strtotime($date) <= strtotime($this->end_date->toDate());
    }
}
