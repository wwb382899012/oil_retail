<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 15:56
 */

namespace ddd\Quota\Repository\LogisticsQuota;


use app\ddd\Common\Repository\RedisCache;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\Infrastructure\Utility;
use ddd\Quota\Domain\LogisticsQuota\ILogisticsDailyQuotaRepository;
use ddd\Quota\Domain\LogisticsQuota\LogisticsDailyQuota;
use ddd\Quota\Domain\LogisticsQuota\LogisticsDailyQuotaService;
use ddd\Quota\Repository\RiskQuotaRepository;

class LogisticsDailyQuotaRepository extends RiskQuotaRepository implements ILogisticsDailyQuotaRepository
{
    use RedisCache;

    public function getNewEntity()
    {
        return new LogisticsDailyQuota();
    }

    public function getActiveRecordClassName()
    {
        return 'LogisticsDailyQuota';
    }

    /**
     * 根据物流企业id获取指定日期物流企业当日额度
     * @param int $logistics_id
     * @param string $date
     * @return bool|\ddd\Common\Domain\BaseEntity|LogisticsDailyQuota|mixed|null
     * @throws \Exception
     */
    public function findByLogisticsId($logistics_id, $date = '')
    {
        if (empty($date))
        {
            $date = Utility::getDate();
        }

        $entity = $this->find('logistics_id=' . $logistics_id . ' and `current_date`="' . $date . '"');
        if (empty($entity))
        {
            $entity = DIService::get(LogisticsDailyQuotaService::class)->createLogisticsDailyQuota($logistics_id);

            /*if (!empty($res))
            {
                $entity = $this->find('logistics_id=' . $logistics_id . ' and current_date="' . $date . '"');
            }*/
        }

        return $entity;
    }

    public function getList($searchParams, $orders)
    {
        // TODO: Implement getList() method.
    }

    /**
     * @param $entity
     * @return mixed
     * @throws \Exception
     */
    public function store($entity)
    {
        $id = $entity->getId();
        if (!empty($id))
        {
            $model = $this->model()->findByPk($id);
            if (empty($model))
            {
                throw new ZModelNotExistsException($id, $this->getActiveRecordClassName());
            }
        }
        else
        {
            $this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new $this->activeRecordClassName;
            $model->current_date = Utility::getDate();
        }
        //这里需要处理一下新增时设置主键值的问题
        $model->setAttributes($entity->getAttributes(), false);
        $this->setModelValue($model, $entity);
        if (!$model->save())
            throw new ZModelSaveFalseException($model);
        $entity->setId($model->getPrimaryKey());
        return $entity;
    }
}
