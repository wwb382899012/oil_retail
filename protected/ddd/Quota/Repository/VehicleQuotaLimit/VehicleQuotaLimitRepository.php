<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/4 0004
 * Time: 17:26
 */

namespace ddd\Quota\Repository\VehicleQuotaLimit;


use app\ddd\Admin\Application\User\UserService;
use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use app\ddd\Common\Repository\RedisCache;
use ddd\Common\Domain\Value\DateTime;

use ddd\Common\Repository\EntityRepository;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\Infrastructure\Utility;
use ddd\Quota\Domain\VehicleQuotaLimit\IVehicleQuotaLimitRepository;
use ddd\Quota\Domain\VehicleQuotaLimit\VehicleQuotaLimit;

class VehicleQuotaLimitRepository extends EntityRepository implements IVehicleQuotaLimitRepository
{
    use RedisCache;
    private $redisKey = '';

    public function getNewEntity()
    {
        return new VehicleQuotaLimit();
    }

    public function getActiveRecordClassName()
    {
        return 'VehicleQuotaLimit';
    }

    public function init()
    {
        //$this->expire_seconds = 86400;
    }

    /**
     * 获取车辆当前可用车辆限额设置
     * @return bool|\ddd\Common\Domain\BaseEntity|VehicleQuotaLimit|mixed|null
     * @throws \Exception
     */
    public function getActiveVehicleQuotaLimit()
    {
        $entity = $this->getEntityFromCache($this->redisKey);

        if ($entity !== false)
        {
            return $entity;
        }

        $entity = $this->find(['condition' => 'status=1', 'order' => 'create_time desc']);
        if (!empty($entity))
        {
            $this->setCache($this->redisKey, $entity);
        }

        return $entity;
    }

    /**
     * 模型转换为实体
     * @param $model
     * @return \ddd\Common\Domain\BaseEntity|VehicleQuotaLimit
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $values = $model->getAttributes(['code', 'rate', 'remark']);
        $entity->setId($model->limit_id);
        $entity->setAttributes($values);
        $entity->setStatus(new Status($model->status, $model->status_time));
        $entity->effect_time = !empty($model->effect_time) ? new DateTime($model->effect_time) : null;
        $entity->create_time = new DateTime($model->create_time);
        $entity->create_user = new Operator($model->create_user_id, DIService::get(UserService::class)->getUser($model->create_user_id)->name);
        $entity->update_time = new DateTime($model->update_time);
        $entity->update_user = new Operator($model->update_user_id, DIService::get(UserService::class)->getUser($model->update_user_id)->name);

        return $entity;
    }

    /**
     * 把对象持久化到数据
     * @param \ddd\Common\IAggregateRoot $entity
     * @return \ddd\Common\IAggregateRoot|mixed
     * @throws ZModelNotExistsException
     * @throws ZModelSaveFalseException
     */
    public function store($entity)
    {
        $id = $entity->getId();
        $this->activeRecordClassName = $this->getActiveRecordClassName();
        $model = new $this->activeRecordClassName;
        if (!empty($id))
        {
            $model = $model::model()->findByPk($id);
            if (empty($model))
            {
                throw new ZModelNotExistsException($id, $this->getActiveRecordClassName());
            }
        }
        $values = $entity->getAttributes(['code', 'rate', 'remark']);
        $model->setAttributes($values);
        $statusEntity = $entity->getStatus();
        if (!empty($statusEntity))
        {
            $model->status = $statusEntity->status;
            $model->status_time = $statusEntity->status_time;
        }
        if (!empty($entity->effect_time))
        {
            $model->effect_time = $entity->effect_time->format();
        }
        if (!empty($entity->create_time))
        {
            $model->create_time = $entity->create_time->format();
        }
        if (!empty($entity->update_time))
        {
            $model->update_time = $entity->update_time->format();
        }
        if (!empty($entity->create_user))
        {
            $model->create_user_id = $entity->create_user->id;
        }
        if (!empty($entity->update_user))
        {
            $model->update_user_id = $entity->update_user->id;
        }
        $res = $model->save();
        if ($res !== true)
        {
            throw new ZModelSaveFalseException($model);
        }
        $entity->setId($model->getPrimaryKey());

        //清除缓存
        $this->clearCache($this->redisKey);

        return $entity;
    }

    /**
     * @desc 更新状态
     * @param   BaseEntity $entity
     * @throws  \Exception
     */
    protected function updateStatus(BaseEntity $entity)
    {
        if (empty($entity))
        {
            ExceptionService::throwArgumentNullException(\Utility::getClassBaseName($entity) . "对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }
        $this->activeRecordClassName = $this->getActiveRecordClassName();
        $modelObj = new $this->activeRecordClassName;
        $model = $modelObj::model()->findByPk($entity->getId());
        if (empty($model))
        {
            throw new ZModelNotExistsException($entity->getId(), \Utility::getClassBaseName($entity));
        }
        $statusEntity = $entity->getStatus();
        if ($model->status != $statusEntity->status)
        {
            $model->status = $statusEntity->status;
            $model->status_time = $statusEntity->status_time;
            $model->update_user_id = \Utility::getNowUserId();
            $model->update_time = Utility::getNow();
            $res = $model->save();
            if ($res !== true)
            {
                throw new ZModelSaveFalseException($model);
            }

            //清除缓存
            $this->clearCache($this->redisKey);
        }
    }
}