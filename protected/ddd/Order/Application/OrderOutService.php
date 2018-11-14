<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/10 0010
 * Time: 9:21
 */

namespace ddd\Order\Application;


use ddd\Common\Application\BaseService;
use ddd\Common\Application\Transaction;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Infrastructure\error\ZException;
use ddd\Order\Domain\Order\OrderRepository;
use \ddd\Order\Domain\Order\OrderService;
use ddd\Order\DTO\Order\OrderDTO;

class OrderOutService extends BaseService
{
    use OrderRepository;
    use Transaction;

    /**
     * 获取司机车辆最大加油量
     * @param $customerId
     * @param $stationId
     * @param $goodsId
     * @return array
     * @throws \Exception
     */
    public function getVehicleMaxOilQuantity($customerId, $stationId, $goodsId = '')
    {
        if (!\Utility::checkQueryId($customerId) || $customerId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'customer_id']);
        }
        if (!\Utility::checkQueryId($stationId) || $stationId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'station_id']);
        }

        try
        {
            return DIService::get(OrderService::class)->getVehicleMaxOilQuantity($customerId, $stationId, $goodsId);
        } catch (\Exception $e)
        {
            throw new ZException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 获取订单详情
     * @param $orderId
     * @return OrderDTO
     * @throws \Exception
     */
    public function getOrderDetail($orderId)
    {
        if (!isset($orderId) || !\Utility::checkQueryId($orderId) || $orderId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'orderId']);
        }

        try
        {
            $order = $this->getOrderRepository()->findById($orderId);
            if (empty($order))
            {
                ExceptionService::throwBusinessException(BusinessError::Order_Not_Exist, ['order_id' => $orderId]);
            }

            $dto = new OrderDTO();
            $dto->fromEntity($order);

            return $dto;
        } catch (\Exception $e)
        {
            throw new ZException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 下单
     * @param $customerId
     * @param $vehicleId
     * @param $stationId
     * @param $goodsId
     * @param $quantity
     * @param $customerTransPassword
     * @param $remark
     * @return int
     * @throws \Exception
     */
    public function doOrder($customerId, $vehicleId, $stationId, $goodsId, $quantity, $customerTransPassword,$remark='')
    {
        if (!isset($customerId) || !\Utility::checkQueryId($customerId) || $customerId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'customerId']);
        }
        if (!isset($vehicleId) || !\Utility::checkQueryId($vehicleId) || $vehicleId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'vehicleId']);
        }
        if (!isset($stationId) || !\Utility::checkQueryId($stationId) || $stationId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'stationId']);
        }
        if (!isset($goodsId) || !\Utility::checkQueryId($goodsId) || $goodsId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'goodsId']);
        }
        if (!isset($quantity) || empty($quantity))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'quantity']);
        }
        if (!isset($customerTransPassword) || empty($customerTransPassword))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'customerTransPassword']);
        }


        try
        {
            $this->beginTransaction();

            $order = DIService::get(OrderService::class)->doOrder($customerId, $vehicleId, $stationId, $goodsId, $quantity, $customerTransPassword,$remark);

            $this->commitTransaction();

            if ($order->getStatusValue() == \Order::STATUS_EFFECTED)
            {
                \AMQPService::publishOrderEffected($order->getId());
            }
            return $order;
        } catch (\Exception $e)
        {
            $this->rollbackTransaction();

            throw new ZException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 获取司机加油订单列表
     * @param $customerId
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws \Exception
     */
    /*public function getCustomerOrders($customerId, $page = 1, $pageSize = 20)
    {
        if (!isset($customerId) || !\Utility::checkQueryId($customerId) || $customerId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'customerId']);
        }

        try
        {
            $customer = DIService::get(CustomerService::class)->getCustomer($customerId);
            if (!empty($customer))
            {
                ExceptionService::throwBusinessException(BusinessError::Customer_Not_Exist, ['customer_id' => $customerId]);
            }

            $search['customer_id'] = $customerId;

            $data = $this->getOrderRepository()->findAllByPage($search, $page, $pageSize);

            return $data;
        } catch (\Exception $e)
        {
            throw new ZException($e->getMessage(), $e->getCode());
        }
    }*/
}