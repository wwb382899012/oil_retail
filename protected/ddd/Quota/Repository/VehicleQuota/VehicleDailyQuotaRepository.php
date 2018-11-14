<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 15:49
 */

namespace ddd\Quota\Repository\VehicleQuota;


use app\ddd\Common\Repository\RedisCache;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\Infrastructure\Utility;
use ddd\Quota\Domain\VehicleQuota\IVehicleDailyQuotaRepository;
use ddd\Quota\Domain\VehicleQuota\VehicleDailyQuota;
use ddd\Quota\Domain\VehicleQuota\VehicleDailyQuotaService;
use ddd\Quota\Repository\RiskQuotaRepository;

class VehicleDailyQuotaRepository extends RiskQuotaRepository implements IVehicleDailyQuotaRepository
{
    use RedisCache;

    public function getNewEntity()
    {
        return new VehicleDailyQuota();
    }

    public function getActiveRecordClassName()
    {
        return 'VehicleDailyQuota';
    }

    /**
     * 获取指定车辆特定日期额度
     * @param $vehicle_id
     * @param string $date
     * @return bool|\ddd\Common\Domain\BaseEntity|mixed|null
     * @throws \Exception
     */
    public function findByVehicleId($vehicle_id, $date = '')
    {
        if (empty($date))
        {
            $date = Utility::getDate();
        }

        $entity = $this->find('vehicle_id='.$vehicle_id.' and `current_date`="'.$date.'"');
        if (empty($entity))
        {
            $entity = DIService::get(VehicleDailyQuotaService::class)->createVehicleDailyQuota($vehicle_id);

            /*if (!empty($res))
            {
                $entity = $this->find('vehicle_id=:vehicleId and current_date=:currentDate', ['vehicleId' => $vehicle_id, 'currentDate' => $date]);
            }*/
        }

        return $entity;
    }

    /**
     * @param VehicleDailyQuota $entity
     * @return VehicleDailyQuota
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