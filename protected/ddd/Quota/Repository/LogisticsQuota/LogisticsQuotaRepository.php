<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 15:46
 */

namespace ddd\Quota\Repository\LogisticsQuota;


use app\ddd\Common\Repository\RedisCache;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Logistics\Domain\LogisticsCompany\ILogisticsCompanyRepository;
use ddd\Quota\Domain\LogisticsQuota\ILogisticsQuotaRepository;
use ddd\Quota\Domain\LogisticsQuota\LogisticsQuota;
use ddd\Quota\Domain\LogisticsQuota\LogisticsQuotaService;
use ddd\Quota\Repository\RiskQuotaRepository;

class LogisticsQuotaRepository extends RiskQuotaRepository implements ILogisticsQuotaRepository
{
    use RedisCache;

    public function getNewEntity()
    {
        return new LogisticsQuota();
    }

    public function getActiveRecordClassName()
    {
        return 'LogisticsQuota';
    }

    public function init()
    {
        parent::init();
        $this->with = array('user');
        //$this->expire_seconds = 86400;
    }

    /**
     * 根据物流企业id获取额度信息
     * @param int $logistics_id
     * @return \ddd\Common\Domain\BaseEntity|LogisticsQuota|null
     * @throws \Exception
     */
    public function findByLogisticsId($logistics_id)
    {
        $entity = $this->find('logistics_id=' . $logistics_id);
        if (empty($entity))
        {
            $res = DIService::get(LogisticsQuotaService::class)->createLogisticsQuota($logistics_id);

            if (!empty($res))
            {
                $entity = $this->find('logistics_id=' . $logistics_id);
            }
        }

        return $entity;
    }

    /**
     * @param $model
     * @return \ddd\Common\Domain\BaseEntity|LogisticsQuota
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $values = $model->getAttributes(['logistics_id', 'credit_quota', 'frozen_quota', 'used_quota']);
        $entity->setId($model->id);
        $entity->setAttributes($values);
        $logisticsEntity = DIService::getRepository(ILogisticsCompanyRepository::class)->findById($model->logistics_id);
        if (empty($logisticsEntity))
        {
            ExceptionService::throwBusinessException(BusinessError::Logistics_Company_Not_Exist, ['logistics_id' => $model->logistics_id]);
        }

        $logisticsCreditQuotaEntity = $logisticsEntity->credit_quota;
        $entity->start_date = $logisticsCreditQuotaEntity->start_date->toDate();
        $entity->end_date = $logisticsCreditQuotaEntity->end_date->toDate();

        return $entity;
    }
}