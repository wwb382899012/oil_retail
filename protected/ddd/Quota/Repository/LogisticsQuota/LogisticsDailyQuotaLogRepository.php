<?php
/**
 * Desc: 物流企业当日额度变更仓储
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 16:10
 */

namespace ddd\Quota\Repository\LogisticsQuota;



use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;

use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\Infrastructure\Utility;
use ddd\Quota\Domain\LogisticsQuota\ILogisticsDailyQuotaLogRepository;
use ddd\Quota\Domain\LogisticsQuota\LogisticsDailyQuotaLog;

class LogisticsDailyQuotaLogRepository extends EntityRepository implements ILogisticsDailyQuotaLogRepository
{
    public function getNewEntity()
    {
        return new LogisticsDailyQuotaLog();
    }

    public function getActiveRecordClassName()
    {
        return 'LogisticsDailyQuotaLog';
    }

    /**
     * 获取额度变更记录对象id
     * @return mixed
     */
    public function getQuotaObjectId(IAggregateRoot $entity)
    {
        return $entity->logistics_id;
    }

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