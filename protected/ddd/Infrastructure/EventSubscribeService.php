<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/11 0011
 * Time: 11:22
 */

namespace ddd\Infrastructure;


use ddd\OilStation\Domain\OilPrice\Event\OilPriceApplyBackedEvent;
use ddd\OilStation\Domain\OilPrice\Event\OilPriceApplyPassedEvent;
use ddd\OilStation\Domain\OilPrice\Event\OilPriceApplySubmittedEvent;
use ddd\OilStation\Domain\OilPrice\OilPriceApplyService;
use ddd\OilStation\Domain\OilStation\Event\OilStationApplyBackedEvent;
use ddd\OilStation\Domain\OilStation\Event\OilStationApplyPassedEvent;
use ddd\OilStation\Domain\OilStation\Event\OilStationApplySubmittedEvent;
use ddd\OilStation\Domain\OilStation\OilStationApplyService;
use ddd\Order\Domain\Order\OrderEffectedEvent;
use ddd\Order\Domain\Order\OrderFailedEvent;
use ddd\Order\Domain\OrderPayment\OrderPaidEvent;
use ddd\Order\Domain\OrderPayment\OrderPaymentEvent;
use ddd\Quota\Application\QuotaService;

class EventSubscribeService
{
    private static $_c;

    /**
     * 返回配置信息
     * @return array
     */
    public static function getConfigs()
    {
        return [
            //油品价格提交
            OilPriceApplySubmittedEvent::class   =>[
                [new OilPriceApplyService(), 'onOilPriceApplySubmitted'],
            ],
            //油品价格审核驳回
            OilPriceApplyBackedEvent::class      =>[
                [new OilPriceApplyService(), 'onOilPriceApplyBacked'],
            ],
            //油品价格审核通过
            OilPriceApplyPassedEvent::class      =>[
                [new OilPriceApplyService(), 'onOilPriceApplyPassed'],
            ],
            //油站准入提交
            OilStationApplySubmittedEvent::class =>[
                [new OilStationApplyService(), 'onOilStationApplySubmitted'],
            ],
            //油站准入审核驳回
            OilStationApplyBackedEvent::class    =>[
                [new OilStationApplyService(), 'onOilStationApplyBacked'],
            ],
            //油站准入审核通过
            OilStationApplyPassedEvent::class    =>[
                [new OilStationApplyService(), 'onOilStationApplyPassed'],
            ],
            //订单生效相关事件处理
            OrderEffectedEvent::class            => [
            ],
            OrderFailedEvent::class             => [

            ],
            //订单付款事件
            OrderPaymentEvent::class => [
                [new QuotaService(), 'onOrderPayment'],
            ],
            //订单付款完成事件
            OrderPaidEvent::class => [

            ],
        ];
    }

    /**
     * 获取需要绑定的事件响应
     * @param $key
     * @return array|null
     */
    public static function getBinds($key)
    {
        if (empty($key))
        {
            return null;
        }

        if (self::$_c[$key])
        {
            return self::$_c[$key];
        }

        $config = static::getConfigs();
        self::$_c[$key] = $config[$key];

        return self::$_c[$key];

    }

    /**
     * 绑定事件处理到对象
     * @param $entity
     * @param $eventName
     * @param null $key 事件绑定的key，默认和事件名一致
     */
    public static function bind($entity, $eventName, $key = null)
    {
        if (empty($key))
        {
            $key = $eventName;
        }
        $binds = static::getBinds($key);
        if (!is_array($binds))
        {
            return;
        }
        foreach ($binds as $v)
        {
            $handler = [];
            if ($v[2] == "static")
            {
                $handler = array($v[0], $v[1]);
            } else
            {
                if (is_string($v[0]))
                {
                    $handler = array(new $v[0], $v[1]);
                } else
                {
                    $handler = array($v[0], $v[1]);
                }
            }

            $entity->attachEventHandler($eventName, $handler);
        }
    }
}