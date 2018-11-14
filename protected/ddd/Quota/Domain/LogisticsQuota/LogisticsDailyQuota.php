<?php
/**
 * Desc: 物流企业当日额度
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 16:25
 */

namespace ddd\Quota\Domain\LogisticsQuota;


use app\ddd\Common\Domain\Value\LogisticsCompany;
use app\ddd\Quota\Application\LogisticsQuotaLimit\LogisticsQuotaLimitService;
use ddd\Common\IAggregateRoot;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Infrastructure\Utility;
use ddd\Quota\Domain\BaseRiskQuota;

class LogisticsDailyQuota extends BaseRiskQuota implements IAggregateRoot
{
    /**
     * 物流企业id
     * @var   int
     */
    public $logistics_id = 0;

    /**
     * 当前日期
     * @var date
     */
    public $current_date;

    use LogisticsDailyQuotaRepository;

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
        $this->repository = $this->getLogisticsDailyQuotaRepository();
    }

    /**
     * 创建
     * @param LogisticsCompany|null $logistics
     * @return LogisticsDailyQuota
     */
    public static function create(LogisticsCompany $logistics = null)
    {
        $entity = new static();
        if (!empty($logistics))
        {
            $entity->logistics_id = $logistics->id;
        }
        $entity->current_date = Utility::getDate();

        return $entity;
    }

    /**
     * 获取当日实际授信额度
     * @return float
     * @throws \Exception
     */
    public function getActualCreditQuota()
    {
        $activeLimitEntity = DIService::get(LogisticsQuotaLimitService::class)->getActiveLogisticsQuotaLimit();
        if (empty($activeLimitEntity))
        {
            ExceptionService::throwBusinessException(BusinessError::Logistics_Quota_Limit_Not_Exist);
        }
        $logisticsQuotaEntity = DIService::getRepository(ILogisticsQuotaRepository::class)->findByLogisticsId($this->logistics_id);
        if (empty($logisticsQuotaEntity))
        {
            ExceptionService::throwBusinessException(BusinessError::Logistics_Quota_Not_Exist, ['logistics_id' => $this->logistics_id]);
        }

        return round($logisticsQuotaEntity->getActualCreditQuota() * $activeLimitEntity->rate);
    }
}
