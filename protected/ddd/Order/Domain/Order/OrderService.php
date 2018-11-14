<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/10 0010
 * Time: 9:21
 */

namespace ddd\Order\Domain\Order;


use app\ddd\Logistics\Application\Driver\DriverService;
use app\ddd\Order\Domain\Goods\Goods;
use ddd\Common\Domain\BaseService;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Infrastructure\error\ZException;
use ddd\Logistics\Domain\Driver\Driver;
use ddd\Logistics\Domain\Driver\IDriverRepository;
use ddd\Logistics\Domain\LogisticsCompany\ILogisticsCompanyRepository;
use ddd\Logistics\Domain\LogisticsCompany\LogisticsCompany;
use ddd\Logistics\Domain\Vehicle\IVehicleRepository;
use ddd\Order\Application\GoodsService;
use ddd\Order\Domain\OrderPayment\OrderPaymentService;
use ddd\Order\DTO\DomainParams\OrderParamsDTO;
use ddd\Order\DTO\DomainParams\vehicleMaxQuantityDTO;
use ddd\Order\DTO\DomainParams\vehicleMaxQuantityItemsDTO;
use ddd\Quota\Domain\LogisticsQuota\ILogisticsDailyQuotaRepository;
use ddd\Quota\Domain\LogisticsQuota\ILogisticsQuotaRepository;
use ddd\Quota\Domain\VehicleQuota\IVehicleDailyQuotaRepository;

class OrderService extends BaseService
{
    use OrderRepository;

    /**
     * 获取司机车辆最大加油量
     * @param $customerId
     * @param $stationId
     * @param string $goodsId
     * @param int $vehicleId
     * @param int $sellPrice
     * @return array
     * @throws \Exception
     */
    public function getVehicleMaxOilQuantity($customerId, $stationId, $goodsId = '', $vehicleId = 0, $sellPrice = 0)
    {
        $res = [];
        if (!\Utility::checkQueryId($customerId) || $customerId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'customer_id']);
        }
        if (!\Utility::checkQueryId($stationId) || $stationId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'station_id']);
        }

        $driverEntity = DIService::getRepository(IDriverRepository::class)->findById($customerId);
        if (empty($driverEntity))
        {
            ExceptionService::throwEntityInstanceNotExistsException($customerId, Driver::class);
        }

        if (!$driverEntity->isActive())
        {
            ExceptionService::throwBusinessException(BusinessError::Driver_Status_Not_Active, ['customer_id' => $customerId]);
        }

        if (\Utility::isNotEmpty($driverEntity->vehicle_items))
        {
            if (!empty($goodsId))
            {
                $activeGoods = $this->checkGoods($stationId, $goodsId);
                if (empty($activeGoods))
                {
                    ExceptionService::throwBusinessException(BusinessError::Oil_Goods_Can_Not_Sell);
                }

                $allGoods[] = $activeGoods;
            } else
            {
                $allGoods = DIService::get(GoodsService::class)->getOilStationAllCanSellGoods($stationId);
            }

            if (\Utility::isEmpty($allGoods))
            {
                ExceptionService::throwBusinessException(BusinessError::Oil_Goods_Can_Sell_Not_Exist);
            }

            foreach ($allGoods as $goods)
            {
                $dto = new vehicleMaxQuantityDTO();
                $dto->goods_id = $goods->goods_id;
                $dto->goods_name = $goods->goods_name;

                $logisticsMaxQuantity = $this->getLogisticsVehicleMaxOilQuantity($customerId, $stationId, $goods->goods_id, $sellPrice);

                if (!empty($vehicleId))
                {
                    $vehicles[] = DIService::getRepository(IVehicleRepository::class)->findById($vehicleId);
                } else
                {
                    $vehicles = $driverEntity->vehicle_items;
                }
                foreach ($vehicles as $vehicle)
                {
                    $vehicle = DIService::getRepository(IVehicleRepository::class)->findById($vehicle->id);
                    if (empty($vehicle))
                    {
                        ExceptionService::throwBusinessException(BusinessError::Vehicle_Not_Exist, ['vehicle_id' => $vehicle->id]);
                    }

                    if ($vehicle->isActive())
                    {
                        $vehicleDailyQuota = DIService::getRepository(IVehicleDailyQuotaRepository::class)->findByVehicleId($vehicle->id);
                        if (empty($vehicleDailyQuota))
                        {
                            ExceptionService::throwBusinessException(BusinessError::Vehicle_Daily_Quota_Not_Exist, ['vehicle_id' => $vehicle->id]);
                        }

                        $vehicleDailyQuantity = $vehicleDailyQuota->getAvailableQuota();
                        $maxQuantity = bccomp($logisticsMaxQuantity, $vehicleDailyQuantity, 2) === 1 ? $vehicleDailyQuantity : $logisticsMaxQuantity;

                        //最大可加油量小于0时，返回0
                        $maxQuantity = bccomp(0, $maxQuantity, 2) === 1 ? 0 : $maxQuantity;

                        $itemDto = new vehicleMaxQuantityItemsDTO();
                        $itemDto->vehicle_id = $vehicle->id;
                        $itemDto->vehicle_number = $vehicle->number;
                        $itemDto->vehicle_model = $vehicle->model;
                        $itemDto->max_available_quantity = $maxQuantity;
                        $dto->items[] = $itemDto;
                    }
                }
                $res[] = $dto;
            }
        }

        return $res;
    }

    /**
     * 获取物流企业对应的车辆最大加油数量
     * @param $customerId
     * @param $stationId
     * @param $goodsId
     * @param int $sellPrice
     * @return float
     * @throws \Exception
     */
    private function getLogisticsVehicleMaxOilQuantity($customerId, $stationId, $goodsId, $sellPrice = 0)
    {
        if (!\Utility::checkQueryId($customerId) || $customerId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'customer_id']);
        }
        if (!\Utility::checkQueryId($stationId) || $stationId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'station_id']);
        }
        if (!\Utility::checkQueryId($goodsId) || $goodsId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'goods_id']);
        }

        $driverEntity = DIService::getRepository(IDriverRepository::class)->findById($customerId);
        if (empty($driverEntity))
        {
            ExceptionService::throwEntityInstanceNotExistsException($customerId, Driver::class);
        }

        if (!$driverEntity->isActive())
        {
            ExceptionService::throwBusinessException(BusinessError::Driver_Status_Not_Active, ['customer_id' => $customerId]);
        }

        $logisticsId = $driverEntity->company->id;
        $logistics = DIService::getRepository(ILogisticsCompanyRepository::class)->findById($logisticsId);
        if (empty($logistics))
        {
            ExceptionService::throwEntityInstanceNotExistsException($logisticsId, LogisticsCompany::class);
        }

        if (!$logistics->isActive())
        {
            ExceptionService::throwBusinessException(BusinessError::Logistics_Status_Not_Active, ['logistics_id' => $logisticsId]);
        }

        if (empty($sellPrice))
        {
            $activeGoods = $this->checkGoods($stationId, $goodsId);
            if (empty($activeGoods))
            {
                //ExceptionService::throwBusinessException(BusinessError::Oil_Price_Active_Not_Exist, ['station_id' => $stationId, 'goods_id' => $goodsId]);
                ExceptionService::throwBusinessException(BusinessError::Oil_Goods_Can_Not_Sell);
            }
            $sellPrice = $activeGoods->price_sell;
        }

        $logisticsQuota = DIService::getRepository(ILogisticsQuotaRepository::class)->findByLogisticsId($logisticsId);
        if (empty($logisticsQuota))
        {
            ExceptionService::throwBusinessException(BusinessError::Logistics_Quota_Not_Exist, ['logistics_id' => $logisticsId]);
        }
        $maxQuota = $logisticsQuota->getAvailableQuota();

        $logisticsDailyQuota = DIService::getRepository(ILogisticsDailyQuotaRepository::class)->findByLogisticsId($logisticsId);
        if (empty($logisticsDailyQuota))
        {
            ExceptionService::throwBusinessException(BusinessError::Logistics_Daily_Quota_Not_Exist, ['logistics_id' => $logisticsId]);
        }
        $dailyQuota = $logisticsDailyQuota->getAvailableQuota();

        $maxQuota = $maxQuota > $dailyQuota ? $dailyQuota : $maxQuota;

        //return round($maxQuota / $sellPrice, 2);
        return substr(sprintf("%.3f",$maxQuota / $sellPrice),0,-1); //最大可加油量不能向上保留小数
    }

    /**
     * 创建订单
     * @param $customerId
     * @param $vehicleId
     * @param $stationId
     * @param $goodsId
     * @param $quantity
     * @param $customerTransPassword
     * @param $remark
     * @return Order
     * @throws \Exception
     */
    public function createOrder($customerId, $vehicleId, $stationId, $goodsId, $quantity, $customerTransPassword,$remark)
    {
        $paramsDto = new OrderParamsDTO();
        $paramsDto->customer_id = $customerId;
        $paramsDto->customer_trans_password = $customerTransPassword;
        $paramsDto->vehicle_id = $vehicleId;
        $paramsDto->station_id = $stationId;
        $paramsDto->goods_id = $goodsId;
        $paramsDto->quantity = $quantity;
        $paramsDto->remark = $remark;
        $activeGoods = $this->checkGoods($stationId, $goodsId);
        if (empty($activeGoods))
        {
            ExceptionService::throwBusinessException(BusinessError::Oil_Goods_Can_Not_Sell);
        } else
        {
            $paramsDto->price_buy = $activeGoods->price_buy;
            $paramsDto->price_sell = $activeGoods->price_sell;
            $paramsDto->price_retail = $activeGoods->price_retail;
        }

        //dto数据校验
        if (!$paramsDto->validate())
        {
            $errors = $paramsDto->getErrors();
            if (\Utility::isNotEmpty($errors))
            {
                foreach ($errors as $error)
                {
                    throw new ZException(BusinessError::Validate_Error, array('reason' => $error[0]));
                }
            }
        }

        $order = Order::create($paramsDto);

        $this->getOrderRepository()->store($order);

        if (!$order->validate())
        {
            $errors = $order->getErrors();
            if (\Utility::isNotEmpty($errors))
            {
                $errors = array_values($errors);
                $this->setOrderFailed($order->getId(), $errors[0][0], $order);

                /*foreach ($errors as $error)
                {
                    //throw new ZException(BusinessError::Validate_Error, array('reason' => $error[0]));
                    $this->setOrderFailed($order->getId(), $error[0], $order);
                }*/
            }
        }

        return $order;
    }

    /**
     * 订单生效
     * @param $orderId
     * @param Order|null $order
     * @throws \Exception
     */
    public function effectOrder($orderId, Order $order = null)
    {
        if (\Utility::checkQueryId($orderId) && $orderId > 0)
        {
            if (empty($order))
            {
                $order = $this->getOrderRepository()->findById($orderId);
                if (empty($order))
                {
                    ExceptionService::throwBusinessException(BusinessError::Order_Not_Exist, ['order_id' => $orderId]);
                }
            }

            if ($order->isCanEffect())
            {
                $order->setEffect();
            }
        } else
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'orderId']);
        }
    }

    /**
     * 订单置为失败
     * @param $orderId
     * @param $failedReason
     * @param Order|null $order
     * @throws \Exception
     */
    public function setOrderFailed($orderId, $failedReason, Order $order = null)
    {
        if (\Utility::checkQueryId($orderId) && $orderId > 0)
        {
            if (empty($order))
            {
                $order = $this->getOrderRepository()->findById($orderId);
                if (empty($order))
                {
                    ExceptionService::throwBusinessException(BusinessError::Order_Not_Exist, ['order_id' => $orderId]);
                }
            }

            $order->failed_reason = $failedReason;
            if ($order->isCanFailed())
            {
                $order->setFailed();
            }
        } else
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'orderId']);
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
     * @return Order
     * @throws \Exception
     */
    public function doOrder($customerId, $vehicleId, $stationId, $goodsId, $quantity, $customerTransPassword,$remark)
    {
        if (!\Utility::checkQueryId($customerId) || $customerId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'customerId']);
        }
        if (!\Utility::checkQueryId($vehicleId) || $vehicleId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'vehicleId']);
        }
        if (!\Utility::checkQueryId($stationId) || $stationId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'stationId']);
        }
        if (!\Utility::checkQueryId($goodsId) || $goodsId <= 0)
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'goodsId']);
        }
        if (empty($quantity))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'quantity']);
        }
        if (empty($customerTransPassword))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'customerTransPassword']);
        }


        $customer = DIService::get(DriverService::class)->getDriver($customerId);
        if (empty($customer))
        {
            ExceptionService::throwBusinessException(BusinessError::Customer_Not_Exist, ['customer_id' => $customerId]);
        }

        $lockKey = 'oil_retail_do_order_payment_lock_' . $customer->logistics_id;
        //生成订单
        try
        {
            //创建订单
            $order = $this->createOrder($customerId, $vehicleId, $stationId, $goodsId, $quantity, $customerTransPassword,$remark);

            if (!empty($order))
            {
                if ($order->isCanPayment())
                {
                    //额度支付
                    if (\Utility::lock($lockKey)) //加锁
                    {
                        $orderId = $order->getId();

                        //订单额度支付
                        $payRes = DIService::get(OrderPaymentService::class)->doPayment(\Order::PAY_TYPE_QUOTA, $order);
                        if ($payRes['code'] == 0)
                        {
                            //支付成功，订单生效
                            $this->effectOrder($orderId, $order);
                        } else
                        {
                            //订单置为失败
                            $this->setOrderFailed($orderId, $payRes['msg'], $order);
                        }

                        \Utility::unlock($lockKey); //解锁
                    } else
                    {
                        ExceptionService::throwBusinessException(BusinessError::System_Busy);
                    }
                }
            } else
            {
                ExceptionService::throwBusinessException(BusinessError::Order_Create_Error);
            }

            return $order;
        } catch (\Exception $e)
        {
            \Utility::unlock($lockKey); //解锁

            throw new ZException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 检查油品是否可售
     * @param $stationId
     * @param $goodsId
     * @return Goods|null
     * @throws \Exception
     */
    private function checkGoods($stationId, $goodsId)
    {
        $goods = Goods::create($stationId, $goodsId);
        if($goods->isActive()) {
            return $goods;
        }
        return null;
    }
}