<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/7 0007
 * Time: 14:21
 */

namespace ddd\Order\Repository\Order;


use app\ddd\Common\Domain\Value\Customer;
use app\ddd\Common\Domain\Value\OilGoods;
use app\ddd\Common\Domain\Value\OilStation;
use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use app\ddd\Common\Domain\Value\Vehicle;
use app\ddd\Common\Repository\RedisCache;
use ddd\Common\Domain\Value\DateTime;
use ddd\Common\Repository\EntityRepository;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\Infrastructure\Utility;
use ddd\Logistics\Domain\Driver\IDriverRepository;
use ddd\Logistics\Domain\Vehicle\IVehicleRepository;
use ddd\OilStation\Domain\OilGoods\IOilGoodsRepository;
use ddd\OilStation\Domain\OilStation\IOilStationRepository;
use ddd\Order\Domain\Order\IOrderRepository;
use ddd\Order\Domain\Order\Order;

class OrderRepository extends EntityRepository implements IOrderRepository
{
    use RedisCache;

    public function getNewEntity()
    {
        return new Order();
    }

    public function getActiveRecordClassName()
    {
        return 'Order';
    }

    public function init()
    {
        $this->expire_seconds = 86400;
    }

    public function findById($id)
    {
        $key = 'order_id_' . $id;
        $entity = $this->getEntityFromCache($key);
        if ($entity !== false)
        {
            return $entity;
        }
        $entity = parent::findById($id);
        if (!empty($entity))
        {
            $this->setCache($key, $entity, $this->expire_seconds);
        }

        return $entity;
    }

    /**
     * 获取客户订单信息
     * @param $customerId
     * @return Order[]
     * @throws \Exception
     */
    public function getByCustomerId($customerId)
    {
        return $this->findAll('customer_id=' . $customerId);
    }

    /**
     * 获取油站订单信息
     * @param $stationId
     * @return Order[]
     * @throws \Exception
     */
    public function getByOilStationId($stationId)
    {
        /*$key = 'station_id_' . $stationId;
        $entity = $this->getEntityFromCache($key);
        if ($entity !== false)
        {
            return $entity;
        }
        $entity = $this->findAll('station_id=' . $stationId);
        if (!empty($entity))
        {
            $this->setCache($key, $entity, $this->expire_seconds);
        }*/

        return $this->findAll('station_id=' . $stationId);
    }

    /**
     * @param $model
     * @return \ddd\Common\Domain\BaseEntity|Order
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $values = $model->getAttributes(['code', 'price_retail', 'price_buy', 'price_sell', 'remark', 'quantity', 'failed_reason']);
        $entity->setAttributes($values);
        $entity->setId($model->order_id);
        $entity->setStatus(new Status($model->status, $model->status_time));

        $customer = DIService::getRepository(IDriverRepository::class)->findById($model->customer_id);
        if (empty($customer))
        {
            ExceptionService::throwBusinessException(BusinessError::Customer_Not_Exist, ['customer_id' => $model->customer_id]);
        }
        $entity->customer = new Customer($customer->customer_id, $customer->name, $customer->phone, $customer->password);

        $entity->logistics = $customer->company;

        $vehicle = DIService::getRepository(IVehicleRepository::class)->findById($model->vehicle_id);
        if (empty($vehicle))
        {
            ExceptionService::throwBusinessException(BusinessError::Vehicle_Not_Exist, ['vehicle_id' => $model->vehicle_id]);
        }
        $entity->vehicle = new Vehicle($vehicle->vehicle_id, $vehicle->number, $vehicle->model);

        $oilStation = DIService::getRepository(IOilStationRepository::class)->findById($model->station_id);
        if (empty($oilStation))
        {
            ExceptionService::throwBusinessException(BusinessError::Oil_Station_Not_Exist, ['station_id' => $model->station_id]);
        }
        $entity->oil_station = new OilStation($oilStation->getId(), $oilStation->name, $oilStation->address);

        $entity->oil_company = $oilStation->getCompany();

        $goods = DIService::getRepository(IOilGoodsRepository::class)->findById($model->goods_id);
        if (empty($goods))
        {
            ExceptionService::throwBusinessException(BusinessError::Oil_Goods_Not_Exist, ['goods_id' => $goods->goods_id]);
        }

        $entity->goods = new OilGoods($goods->goods_id, $goods->name);

        $entity->effect_time = !empty($model->effect_time) ? new DateTime($model->effect_time) : null;
        $entity->create_user = new Operator($model->create_user_id);
        $entity->create_time = new DateTime($model->create_time);
        $entity->update_user = new Operator($model->update_user_id);
        $entity->update_time = new DateTime($model->update_time);
        //if(is_numeric(strpos($entity->remark,'人工补单')))
        if(!empty($entity->remark))
            $entity->order_type="补单";
        else
            $entity->order_type="正常";

        return $entity;
    }

    /**
     * 保存
     * @param \ddd\Common\IAggregateRoot $entity
     * @return \ddd\Common\IAggregateRoot
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
            /*if (empty($model))
            {
                $this->activeRecordClassName = $this->getActiveRecordClassName();
                $model = new $this->activeRecordClassName;
            }*/
        } else {
            $this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new $this->activeRecordClassName;
        }

        //这里需要处理一下新增时设置主键值的问题
        $model->setAttributes($entity->getAttributes(['code', 'price_buy', 'price_sell', 'price_retail', 'remark', 'quantity', 'failed_reason']), false);
        $this->setModelValue($model, $entity);
        $statusEntity = $entity->getStatus();
        if (!empty($statusEntity))
        {
            $model->status = $statusEntity->status;
            $model->status_time = $statusEntity->status_time;
        }
        if (!empty($entity->customer))
        {
            $model->customer_id = $entity->customer->id;
        }
        if (!empty($entity->logistics))
        {
            $model->logistics_id = $entity->logistics->id;
        }
        if (!empty($entity->vehicle))
        {
            $model->vehicle_id = $entity->vehicle->id;
        }
        if (!empty($entity->oil_station))
        {
            $model->station_id = $entity->oil_station->id;
        }
        if (!empty($entity->oil_company))
        {
            $model->oil_company_id = $entity->oil_company->id;
        }
        if (!empty($entity->goods))
        {
            $model->goods_id = $entity->goods->id;
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
        if (!$model->save())
        {
            throw new ZModelSaveFalseException($model);
        }
        $entity->setId($model->getPrimaryKey());

        //清除缓存
        $key = 'order_id_' . $id;
        $this->clearCache($key);

        return $entity;
    }

    /**
     * 更新状态
     * @param $entity
     * @throws ZModelNotExistsException
     * @throws ZModelSaveFalseException
     */
    protected function updateStatus($entity)
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
        if (!empty($entity->failed_reason))
        {
            $model->failed_reason = $entity->failed_reason;
        }
        if (!empty($entity->effect_time))
        {
            $model->effect_time = $entity->effect_time;
        }
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
            $key = 'order_id_' . $entity->getId();
            $this->clearCache($key);
        }
    }

    /**
     * 订单生效
     * @param Order $order
     * @throws \Exception
     */
    public function effect(Order $order)
    {
        $this->updateStatus($order);
    }

    /**
     * 订单失败
     * @param Order $order
     * @throws \Exception
     */
    public function failed(Order $order)
    {
        $this->updateStatus($order);
    }
}