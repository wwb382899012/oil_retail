<?php
/**
 * Desc: 物流企业额度
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 16:25
 */

namespace ddd\Quota\Domain\LogisticsQuota;


use app\ddd\Common\Domain\Value\LogisticsCompany;
use ddd\Common\IAggregateRoot;
use ddd\Quota\Domain\BaseRiskQuota;

class LogisticsQuota extends BaseRiskQuota implements IAggregateRoot
{
    /**
     * 物流企业id
     * @var   int
     */
    public $logistics_id = 0;

    /**
     * 开始日期
     * @var   date
     */
    public $start_date;

    /**
     * 结束日期
     * @var   date
     */
    public $end_date;

    use LogisticsQuotaRepository;

    public function getIdName()
    {
        return "id";
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function init()
    {
        $this->repository = $this->getLogisticsQuotaRepository();
    }

    /**
     * 创建
     * @param LogisticsCompany|null $logistics
     * @return LogisticsQuota
     */
    public static function create(LogisticsCompany $logistics = null)
    {
        $entity = new static();
        if (!empty($logistics))
        {
            $entity->logistics_id = $logistics->id;
        }

        return $entity;
    }

    /**
     * 获取实际授信额度
     * @return   float
     */
    public function getActualCreditQuota()
    {
        $currTimeStamp = strtotime(\Utility::getDate());
        if (strtotime($this->start_date) <= $currTimeStamp && $currTimeStamp <= strtotime($this->end_date))
        {
            return $this->credit_quota;
        }

        return 0;
    }
}
