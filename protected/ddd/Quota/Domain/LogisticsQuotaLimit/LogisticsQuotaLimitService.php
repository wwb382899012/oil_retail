<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/5 0005
 * Time: 10:20
 */

namespace ddd\Quota\Domain\LogisticsQuotaLimit;


use app\ddd\Common\Domain\Value\Operator;
use ddd\Common\Domain\BaseService;
use ddd\Common\Domain\Value\DateTime;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;

class LogisticsQuotaLimitService extends BaseService
{
    use LogisticsQuotaLimitRepository;

    /**
     * 创建限额
     * @param $quotaRate
     * @return LogisticsQuotaLimit
     * @throws \Exception
     */
    public function createLogisticsQuotaLimit($quotaRate)
    {
        $entity = LogisticsQuotaLimit::create($quotaRate);
        if (!$entity->validate())
        {
            $errors = $entity->getErrors();
            if (\Utility::isNotEmpty($errors))
            {
                foreach ($errors as $error)
                {
                    ExceptionService::throwBusinessException(BusinessError::Validate_Error, array('reason' => $error[0]));
                }
            }
        }
        $entity->create_user = new Operator(\Utility::getNowUserId(), \Utility::getNowUserName());
        $entity->create_time = new DateTime();
        $entity->update_user = new Operator(\Utility::getNowUserId(), \Utility::getNowUserName());
        $entity->update_time = new DateTime();
        $entity->effect_time = new DateTime();
        $logisticsQuotaLimitEntity = $this->getLogisticsQuotaLimitRepository()->store($entity);

        return $logisticsQuotaLimitEntity;
    }

    /**
     * 获取当前可用限额设置
     * @return LogisticsQuotaLimit
     * @throws \Exception
     */
    public function getActiveLogisticsQuotaLimit()
    {
        $entity = $this->getLogisticsQuotaLimitRepository()->getActiveLogisticsQuotaLimit();
        if (empty($entity))
        {
            $entity = new LogisticsQuotaLimit();
            $entity->rate = 0;
        }

        return $entity;
    }
}