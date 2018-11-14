<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/11 0011
 * Time: 11:08
 */

namespace ddd\Order\Domain\OrderPayment;


use ddd\Common\Domain\BaseService;
use ddd\Common\Domain\Value\DateTime;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Infrastructure\error\ZException;
use ddd\Order\Domain\Order\IOrderRepository;
use ddd\Order\Domain\Order\Order;
use ddd\Quota\Application\LogisticsQuota\LogisticsQuotaService;
use ddd\Quota\Application\VehicleQuota\VehicleDailyQuotaService;

class OrderPaymentService extends BaseService
{
    /**
     * 订单付款
     * @param $payType
     * @param Order $order
     * @return array(
     *      'code' => '错误码'
     *      'msg' => '错误信息'
     * )
     * @throws \Exception
     */
    public function doPayment($payType, Order $order)
    {
        $res = ['code' => 0, 'msg' => ''];
        if (!isset($payType))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'payType']);
        }
        if (empty($order))
        {
            ExceptionService::throwArgumentNullException("Order对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }

        if ($payType == \Order::PAY_TYPE_QUOTA)
        {
            $checkRes = $this->checkQuotaPayEnough($order);
            if ($checkRes['code'] != 0)
            {
                return $checkRes;
            }
        }

        $payment = $this->createPayment($payType, $order->getId(), $order->getOrderSellAmount());

        $result = $payment->doPay();
        if ($result === true)
        {
            $payment->setPaid();
        }

        return $res;
    }

    /**
     * 检查额度支付额度是否足够
     * @param Order $order
     * @return array(
     *      'code' => '错误码'
     *      'msg' => '错误信息'
     * )
     * @throws \Exception
     */
    public function checkQuotaPayEnough(Order $order)
    {
        if (empty($order))
        {
            ExceptionService::throwArgumentNullException("Order对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }
        $res = ['code' => 0, 'msg' => ''];
        $logisticsQuotaService = DIService::get(LogisticsQuotaService::class);
        $logisticsId = $order->logistics->id;
        $logisticsQuota = $logisticsQuotaService->getLogisticsQuota($logisticsId);
        $sellAmount = $order->getOrderSellAmount();
        if (empty($res['msg']) && bccomp($sellAmount, $logisticsQuota->available_quota) === 1)
        {
            $res = ['code' => - 1, 'msg' => '订单售价超出物流企业可用额度'];
        }

        $logisticsDailyQuota = $logisticsQuotaService->getLogisticsDailyQuota($logisticsId);
        if (empty($res['msg']) && bccomp($sellAmount, $logisticsDailyQuota->available_quota) === 1)
        {
            $res = ['code' => - 1, 'msg' => '订单售价超出物流企业当日可用额度'];
        }

        $vehicleDailyQuota = DIService::get(VehicleDailyQuotaService::class)->getVehicleDailyQuota($order->vehicle->id);
        if (empty($res['msg']) && bccomp($order->quantity, $vehicleDailyQuota->available_quota) === 1)
        {
            $res = ['code' => - 1, 'msg' => '订单加油量超出车辆当日可用额度'];
        }

        return $res;
    }

    /**
     * 创建订单付款
     * @param $payType
     * @param $orderId
     * @param $amount
     * @return OrderPayment
     * @throws \Exception
     */
    public function createPayment($payType, $orderId, $amount)
    {
        if (!isset($payType))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'payType']);
        }
        if (!isset($orderId))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'orderId']);
        }
        if (!isset($amount))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'amount']);
        }

        $order = DIService::getRepository(IOrderRepository::class)->findById($orderId);
        if (empty($order))
        {
            ExceptionService::throwBusinessException(BusinessError::Order_Not_Exist, ['order_id' => $orderId]);
        }

        if (!$order->isCanPayment())
        {
            ExceptionService::throwBusinessException(BusinessError::Order_Status_Not_Allow_Payment, ['order_id' => $orderId]);
        }

        $payment = OrderPayment::create($payType, $orderId, $amount);
        $payment->create_time = new DateTime();
        $payment->create_user = $order->create_user;

        if (!$payment->validate())
        {
            $errors = $payment->getErrors();
            if (\Utility::isNotEmpty($errors))
            {
                foreach ($errors as $error)
                {
                    throw new ZException(BusinessError::Validate_Error, array('reason' => $error[0]));
                }
            }
        }

        return $payment;
    }
}