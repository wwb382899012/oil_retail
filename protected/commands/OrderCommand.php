<?php
/**
 * Created by vector.
 * DateTime: 2018/9/11 14:24
 * Describe：
 */

class OrderCommand extends AMQPCommand
{
    /**
     * 需要监听的队列信息
     * @var array
     */
    protected $queueConfig = array(
        'oil.retail.push.order.to.finance' => array( //推送生效订单到财务系统
            "fn" => "pushOrderToFinance",
            "exchange" => "oil.retail.direct",
            "routingKey" => AMQPService::OIL_RETAIL_ROUTING_KEY_ORDER_EFFECTED,
        ),
        'oil.retail.send.order.effected.reminder' => array( //发送订单生效提醒
            "fn" => "sendOrderReminder",
            "exchange" => "oil.retail.direct",
            "routingKey" => AMQPService::OIL_RETAIL_ROUTING_KEY_ORDER_EFFECTED,
        )
    );

    public function init()
    {
        $this->sleepTime = 1;
        $this->maxTaskPerChild = 2000;
        parent::init();
    }

    /**
     * 推送生效订单到财务
     * @param $orderId
     * @throws Exception
     */
    public function pushOrderToFinance($orderId)
    {
        if (isset($orderId) && Utility::checkQueryId($orderId) && $orderId > 0)
        {
            $order = \ddd\Infrastructure\DIService::get(\ddd\Order\Application\OrderOutService::class)->getOrderDetail($orderId);
            if (!empty($order))
            {
                if ($order->status == Order::STATUS_EFFECTED)
                {
                    $params['ord_id'] = $order->code;
                    $params['oil_code'] = $order->oil_station->id;
                    $params['logi_code'] = $order->logistics->id;
                    $params['customer_id'] = $order->customer->id;
                    $params['customer_name'] = $order->customer->name;
                    $params['car_model'] = $order->vehicle->model;

                    //$isRepair = strpos($order->remark,'工补单:');
                    if(!empty($order->remark))
                        $params['status'] = 3;//补单
                    else
                        $params['status'] = 1;//正常下单

                    $params['plate_info'] = $order->vehicle->number;
                    $params['ord_create_time'] = str_replace('-', '/', $order->create_time);
                    $params['ord_pay_time'] = str_replace('-', '/', $order->effect_time);
                    $params['oil_type'] = $order->goods->name;
                    $params['refuel_number'] = $order->quantity;
                    $params['retail_price'] = $order->retail_price;
                    $params['logi_price'] = $order->discount_price;
                    $params['oils_price'] = $order->agreed_price;

                    $systemUrl = Mod::app()->params['oil_finance_url'];
                    $postUrl = $systemUrl . '/interface/receiveOrder';

                    Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 推送生效订单到财务入参:' . json_encode($params) . ' || 接口地址:' . $postUrl);
                    $res = Utility::postCMD($params, $postUrl);
                    Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 推送生效订单到财务结果:' . json_encode($res));

                    if (!isset($res['code']) || $res['code'] != 0)
                    {
                        throw new Exception('接口调用失败！');
                    }
                }
            }
        }
    }

    /**
     * 订单生效发送短信提醒
     * @param $orderId
     * @throws Exception
     */
    public function sendOrderReminder($orderId)
    {
        if (isset($orderId) && Utility::checkQueryId($orderId) && $orderId > 0)
        {
            $order = \ddd\Infrastructure\DIService::get(\ddd\Order\Application\OrderOutService::class)->getOrderDetail($orderId);
            if (!empty($order))
            {
                if ($order->status == Order::STATUS_EFFECTED)
                {
                    $oilContacts = OilContact::model()->findByStationId($order->oil_station->id);
                    if (Utility::isNotEmpty($oilContacts))
                    {
                        foreach ($oilContacts as $contact)
                        {
                            if (!empty($contact->phone))
                            {
                                $smsParams['vehicle_number'] = $order->vehicle->number;
                                $smsParams['trans_time'] = $order->create_time;
                                $smsParams['quantity'] = $order->quantity;
                                $smsParams['order_code'] = $order->code;

                                SmsService::sendSms($contact->phone, 103, $smsParams);
                            }
                        }
                    }
                }
            }
        }
    }
}