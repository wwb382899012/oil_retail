<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/11 0011
 * Time: 11:44
 */

namespace ddd\Quota\Application\LogisticsQuota;


use ddd\Common\Application\BaseService;
use ddd\Common\Application\Transaction;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Infrastructure\error\ZException;
use ddd\Infrastructure\Utility;
use ddd\Quota\Domain\LogisticsQuota\ILogisticsDailyQuotaRepository;
use ddd\Quota\Domain\LogisticsQuota\ILogisticsQuotaRepository;
use ddd\Quota\DTO\LogisticsQuota\LogisticsDailyQuotaDTO;
use ddd\Quota\DTO\LogisticsQuota\LogisticsQuotaDTO;

class LogisticsQuotaService extends BaseService
{
    use Transaction;

    /**
     * 获取物流企业当日额度信息
     * @param $logisticsId
     * @param string $date
     * @return LogisticsDailyQuotaDTO
     * @throws \Exception
     */
    public function getLogisticsDailyQuota($logisticsId, $date = '')
    {
        if (!isset($logisticsId) || !\Utility::checkQueryId($logisticsId) || $logisticsId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'logisticsId']);
        }

        try
        {
            if (empty($date))
            {
                $date = Utility::getDate();
            }

            $entity = DIService::getRepository(ILogisticsDailyQuotaRepository::class)->findByLogisticsId($logisticsId, $date);
            if (empty($entity))
            {
                ExceptionService::throwBusinessException(BusinessError::Logistics_Daily_Quota_Not_Exist, ['logistics_id' => $logisticsId]);
            }

            $dto = new LogisticsDailyQuotaDTO();
            $dto->fromEntity($entity);

            return $dto;
        } catch (\Exception $e)
        {
            throw new ZException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 获取物流企业额度信息
     * @param $logisticsId
     * @return LogisticsQuotaDTO
     * @throws \Exception
     */
    public function getLogisticsQuota($logisticsId)
    {
        if (!isset($logisticsId) || !\Utility::checkQueryId($logisticsId) || $logisticsId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'logisticsId']);
        }

        try
        {
            $entity = DIService::getRepository(ILogisticsQuotaRepository::class)->findByLogisticsId($logisticsId);
            if (empty($entity))
            {
                ExceptionService::throwBusinessException(BusinessError::Logistics_Quota_Not_Exist, ['logistics_id' => $logisticsId]);
            }

            $dto = new LogisticsQuotaDTO();
            $dto->fromEntity($entity);

            return $dto;
        } catch (\Exception $e)
        {
            throw new ZException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 创建物流企业额度信息
     * @param $logisticsId
     * @throws ZException
     */
    public function createLogisticsQuota($logisticsId)
    {
        if (!isset($logisticsId) || !\Utility::checkQueryId($logisticsId) || $logisticsId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'logisticsId']);
        }

        try
        {
            $this->beginTransaction();

            DIService::get(\ddd\Quota\Domain\LogisticsQuota\LogisticsQuotaService::class)->createLogisticsQuota($logisticsId);

            $this->commitTransaction();
        } catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw new ZException($e->getMessage(), $e->getCode());
        }
    }
}