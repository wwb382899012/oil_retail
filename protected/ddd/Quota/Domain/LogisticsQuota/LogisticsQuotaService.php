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

class LogisticsQuotaService extends BaseRiskQuotaService
{
    use LogisticsQuotaLogRepository;
    use LogisticsQuotaRepository;

    public function init()
    {
        $this->logRepository = $this->getLogisticsQuotaLogRepository();
    }

    /**
     * 创建物流企业额度
     * @param $logisticsId
     * @return mixed
     * @throws \Exception
     */
    public function createLogisticsQuota($logisticsId)
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

        $logisticsQuotaEntity = LogisticsQuota::create(new LogisticsCompany($logisticsId));

        $logisticsQuotaEntity->credit_quota = $logisticsEntity->credit_quota->credit_quota->amount;

        $entity = $this->getLogisticsQuotaRepository()->store($logisticsQuotaEntity);

        return $entity;
    }
}