<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/7 0007
 * Time: 10:16
 */

namespace ddd\Quota\Application;


use ddd\Common\Application\BaseService;
use ddd\Common\Application\Transaction;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Infrastructure\error\ZException;
use ddd\Logistics\Domain\LogisticsCompany\ILogisticsCompanyRepository;
use ddd\Order\Domain\Order\IOrderRepository;
use ddd\Order\Domain\Order\Order;
use ddd\Order\Domain\OrderPayment\OrderPaymentEvent;
use ddd\Quota\Domain\RiskQuotaService;
use ddd\Quota\Domain\RiskQuotaSourceCategoryEnum;
use ddd\Quota\DTO\DomainParams\LogisticsRepayParamsDTO;
use ddd\Quota\DTO\DomainParams\OrderPaymentParamsDTO;

class QuotaService extends BaseService
{
    use Transaction;

    /**
     * 订单支付时
     * @param OrderPaymentEvent $event
     * @return bool
     * @throws ZException
     */
    public function onOrderPayment(OrderPaymentEvent $event)
    {
        if (empty($event))
        {
            ExceptionService::throwArgumentNullException("OrderPaymentEvent对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }

        $orderPayment = $event->sender;
        $orderId = $orderPayment->order_id;
        if (\Utility::checkQueryId($orderId) && $orderId > 0)
        {
            $orderEntity = DIService::getRepository(IOrderRepository::class)->findById($orderId);
            if (empty($orderEntity))
            {
                ExceptionService::throwEntityInstanceNotExistsException($orderId, Order::class);
            }

            $dto = new OrderPaymentParamsDTO();
            $dto->logistics_id = $orderEntity->logistics->id;
            $dto->relation_id = $orderId;
            $dto->category = RiskQuotaSourceCategoryEnum::Order_Payment;
            $dto->amount = - 1 * $orderPayment->amount;
            $dto->vehicle_id = $orderEntity->vehicle->id;
            $dto->quantity = - 1 * $orderEntity->quantity;
            $dto->remark = '订单支付';

            try
            {
                $this->beginTransaction();

                DIService::get(RiskQuotaService::class)->onOrderPayment($dto);

                $this->commitTransaction();

                return true;
            } catch (\Exception $e)
            {
                $this->rollbackTransaction();

                throw new ZException($e->getMessage(), $e->getCode());
            }
        } else
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'order_id']);
        }
    }

    /**
     * 当物流企业还款时
     * @param LogisticsRepayEvent $event
     * @return bool
     * @throws ZException
     */
    public function onLogisticsRepay(LogisticsRepayEvent $event)
    {
        if (empty($event))
        {
            ExceptionService::throwArgumentNullException("LogisticsRepayEvent对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }
        $entity = $event->sender;
        $logisticsId = $entity->logistics_id;
        if (\Utility::checkQueryId($logisticsId) && $logisticsId > 0)
        {
            $logisticsEntity = DIService::getRepository(ILogisticsCompanyRepository::class)->findById($logisticsId);
            if (empty($logisticsEntity))
            {
                ExceptionService::throwEntityInstanceNotExistsException($logisticsId, LogisticsCompany::class);
            }
            $dto = new LogisticsRepayParamsDTO();
            $dto->relation_id = $entity->getId();
            $dto->logistics_id = $logisticsId;
            $dto->amount = $entity->amount;
            $dto->category = RiskQuotaSourceCategoryEnum::Logistics_Repay;
            $dto->remark = '物流还款';

            try
            {
                $this->beginTransaction();

                DIService::get(RiskQuotaService::class)->onLogisticsRepay($dto);

                $this->commitTransaction();

                return true;
            } catch (\Exception $e)
            {
                $this->rollbackTransaction();

                throw new ZException($e->getMessage(), $e->getCode());
            }
        } else
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'logistics_id']);
        }
    }
}