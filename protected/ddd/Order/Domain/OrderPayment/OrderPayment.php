<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/7 0007
 * Time: 14:30
 */

namespace ddd\Order\Domain\OrderPayment;


use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\Common\Domain\Value\DateTime;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Infrastructure\Utility;

class OrderPayment extends BaseEntity implements IAggregateRoot
{
    /**
     * 标识
     * @var   OrderId
     */
    public $payment_id;

    /**
     * 1: 信用支付
     * 支付方式
     * @var   int
     */
    public $pay_type = 0;

    /**
     * 订单id
     * @var   int
     */
    public $order_id = 0;

    /**
     * 支付金额
     * @var   int
     */
    public $amount = 0;

    /**
     * 备注
     * @var   string
     */
    public $remark;

    /**
     * 状态
     * @var   Status
     */
    protected $status;

    /**
     * 生效时间
     * @var   Datetime
     */
    public $effect_time;

    /**
     * 创建时间
     * @var   DateTime
     */
    public $create_time;

    /**
     * 更新用户
     * @var   Operator
     */
    public $update_user;

    /**
     * 更新时间
     * @var   Datetime
     */
    public $update_time;

    /**
     * 创建用户
     * @var   Operator
     */
    public $create_user;

    /**
     * 订单支付事件
     */
    const EVENT_PAYMENT = "onPayment";
    /**
     * 订单支付事件之后
     */
    const EVENT_PAID = "onPaid";

    /**
     * 事件配置，事件名必须以on开头，否则无效
     * @return array
     */
    protected function events()
    {
        return [static::EVENT_PAYMENT, static::EVENT_PAID];
    }

    function getId()
    {
        return $this->payment_id;
    }

    function setId($value)
    {
        $this->payment_id = $value;
    }

    /**
     * 创建
     * @param $payType
     * @param $orderId
     * @param $amount
     * @return OrderPayment
     * @throws \Exception
     */
    public static function create($payType, $orderId, $amount)
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

        $entity = new static();
        $entity->pay_type = $payType;
        $entity->order_id = $orderId;
        $entity->amount = $amount;
        $entity->setStatus(new Status(OrderPaymentStatusEnum::Status_New, Utility::getDateTime()));
        $entity->create_time = new DateTime();

        return $entity;
    }

    /**
     * 是否可支付完成
     * @return bool
     */
    public function isCanPaid()
    {
        return $this->getStatusValue() < OrderPaymentStatusEnum::Status_Done;
    }

    /**
     * 是否可支付
     * @return bool
     */
    public function isCanPay()
    {
        return $this->getStatusValue() == OrderPaymentStatusEnum::Status_New;
    }

    /**
     * 订单支付
     * @param bool $persistent
     * @return bool
     * @throws \Exception
     */
    public function doPay($persistent = true)
    {
        if ($this->isCanPaid())
        {
            $this->status = new Status(OrderPaymentStatusEnum::Status_In_Payment, Utility::getDateTime());
            if ($persistent)
            {
                //暂无仓储，不做持久化处理
            }

            $this->publishEvent(static::EVENT_PAYMENT, new OrderPaymentEvent($this));

            return true;
        } else
        {
            ExceptionService::throwBusinessException(BusinessError::Order_Payment_Status_Not_Allow_Payment);
        }
    }

    /**
     * 设为已支付
     * @param bool $persistent
     * @throws \Exception
     */
    public function setPaid($persistent = true)
    {
        if ($this->isCanPaid())
        {
            $this->status = new Status(OrderPaymentStatusEnum::Status_Done, Utility::getDateTime());
            $this->effect_time = new DateTime();
            if ($persistent)
            {
                //暂无仓储，不做持久化处理
            }

            $this->publishEvent(static::EVENT_PAID, new OrderPaidEvent($this));
        } else
        {
            ExceptionService::throwBusinessException(BusinessError::Order_Payment_Status_Not_Allow_Paid);
        }
    }

    /**
     * 设置状态
     * @param    Status $status
     */
    public function setStatus(Status $status)
    {
        $this->status = $status;
    }

    /**
     * 获取状态
     * @return   Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * 获取状态值
     * @return int
     */
    public function getStatusValue()
    {
        return $this->status->status;
    }
}