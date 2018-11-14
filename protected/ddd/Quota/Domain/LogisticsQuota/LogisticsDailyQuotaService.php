<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/5 0005
 * Time: 19:45
 */

namespace ddd\Quota\Domain\LogisticsQuota;


use app\ddd\Common\Domain\Value\LogisticsCompany;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Logistics\Domain\LogisticsCompany\ILogisticsCompanyRepository;
use ddd\Quota\Domain\BaseRiskQuotaService;

class LogisticsDailyQuotaService extends BaseRiskQuotaService
{
    use LogisticsDailyQuotaRepository;
    use LogisticsDailyQuotaLogRepository;

    public function init()
    {
        $this->logRepository = $this->getLogisticsDailyQuotaLogRepository();
    }

    /**
     * 创建物流企业当日额度
     * @param $logisticsId
     * @return mixed
     * @throws \Exception
     */
    public function createLogisticsDailyQuota($logisticsId)
    {
        if (empty($logisticsId))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'logistics_id']);
        }
        $logisticsEntity = DIService::getRepository(ILogisticsCompanyRepository::class)->findById($logisticsId);
        if (empty($logisticsEntity))
        {
            ExceptionService::throwBusinessException(BusinessError::Logistics_Company_Not_Exist, ['logistics_id' => $logisticsId]);
        }

        $entity = LogisticsDailyQuota::create(new LogisticsCompany($logisticsId));

        $entity = $this->getLogisticsDailyQuotaRepository()->store($entity);

        return $entity;
    }
}