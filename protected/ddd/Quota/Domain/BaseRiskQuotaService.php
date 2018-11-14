<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/5 0005
 * Time: 19:00
 */

namespace ddd\Quota\Domain;


use ddd\Common\Domain\BaseService;
use ddd\Common\Domain\Value\DateTime;
use ddd\Common\Repository\EntityRepository;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;

class BaseRiskQuotaService extends BaseService
{
    /**
     * 额度变更日志仓储
     * @var   EntityRepository
     */
    protected $logRepository;

    /**
     * 更新额度
     * @param    BaseRiskQuota $entity
     * @param    int $quota
     * @param    int $category
     * @param    int $relation_id
     * @param    string $remark
     * @throws   \Exception
     */
    public function updateQuota($entity, $quota, $category, $relation_id, $remark = '')
    {
        if (empty($entity))
        {
            ExceptionService::throwArgumentNullException("BaseRiskQuota对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }

        if (!isset($quota))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'quota']);
        }

        if (!isset($category))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'category']);
        }

        if (!isset($relation_id))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'relation_id']);
        }

        if ($quota != 0)
        {
            if ($quota > 0)
            {
                $entity->addUsedQuota($quota);
            } else
            {
                $entity->subtractUsedQuota($quota);
            }

            $logEntity = $this->createRiskQuotaLogEntity();
            $logEntity->quota = abs($quota);
            $logEntity->quota_total = $entity->getAvailableQuota();
            $logEntity->initMethod($quota);
            $logEntity->category = $category;
            $logEntity->relation_id = $relation_id;
            $logEntity->remark = $remark;
            $logEntity->create_time = new DateTime();
            $quotaObjectPropertyName = $logEntity->getQuotaObjectPropertyName();
            $logEntity->$quotaObjectPropertyName = $this->logRepository->getQuotaObjectId($entity);
            $this->logRepository->store($logEntity);
        }
    }

    /**
     * 创建变更记录对象
     * @return mixed
     */
    protected function createRiskQuotaLogEntity()
    {
        return $this->logRepository->getNewEntity();
    }
}