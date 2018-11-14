<?php

/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2016/3/9
 * Time: 15:23
 * Describe：
 */
class AMQPService
{

    const OIL_RETAIL_ROUTING_KEY_ORDER_EFFECTED = 'oil.retail.order.effected';

    const QUEUE_OIL_STATION_TO_FINANCE_SYSTEM = 'oil.retail.push.oil.station.to.finance.system';

    const QUEUE_LOGISTICS_COMPANY_TO_FINANCE_SYSTEM = 'oil.retail.push.logistics.company.to.finance.system';

    private static $exchanges=array();

    /**
     * 发布队列消息
     * @param $exchange
     * @param $routeKey
     * @param $message
     * @return bool
     */
    public static function publish($exchange,$routeKey,$message)
    {
        try {
            $obj = self::getExchange($exchange);
            return $obj->publish($message, $routeKey);
        }
        catch(Exception $e)
        {
            Mod::log("AQMP Publish Message Error: ".$e->getMessage(),"error");
            return false;
        }
    }

    /**
     * 获取exchange
     * @param $exchangeName
     * @return mixed
     */
    protected static function getExchange($exchangeName)
    {
        if(empty(self::$exchanges[$exchangeName]))
        {
            $obj=Mod::app()->amqp->exchange($exchangeName);
            self::$exchanges[$exchangeName]=$obj;
        }
        return self::$exchanges[$exchangeName];
    }

    /**
     * 发布到延迟队列
     * @param $queueName
     * @param $message
     * @param $seconds
     * @param $routeKey
     * @return null
     */
    public static function publishToDelayQueue($queueName,$message,$seconds,$routeKey="")
    {
        $params=array(
            "queueName"=>$queueName,
            "seconds"=>$seconds,
            "message"=>$message,
            "routingKey"=>$routeKey,
        );
        return self::addToDelayQueue($params);
    }

    /**
     * 调用接口命令
     * @param $params
     * @return mixed
     */
    public static function cmd($params)
    {
        $url=Mod::app()->params["delay_amqp_url"];
        return Utility::cmd($params,$url);
    }

    /**
     * 增加到延时队列
     * @param $params
     * @return bool
     */
    public static function addToDelayQueue($params)
    {
        $data=array(
            "cmd"=>"14010000",
            "tag"=>1,
            "queue_name"=>$params["queueName"],
            "wait_seconds"=>$params["seconds"],
            "message"=>$params["message"],
            "routing_key"=>$params["routingKey"],
        );
        $res=self::cmd($data);
        if(!empty($res) && $res["code"]==0)
        {
            return true;
        }
        else
        {
            Mod::log("发布消息到延时队列[14010000]接口出错，参数：".json_encode($data)."，错误信息：".$res["msg"],"error");
            return false;
        }
    }

    

    /**
     * 发送邮件
     * @param $userId
     * @param $subject
     * @param $content
     * @param array $attachArray
     * @return bool
     */
    public static function publishEmail($userId,$subject,$content,$attachArray=array())
    {
        $data=array("userId"=>$userId,"subject"=>$subject,"content"=>$content,"attach"=>$attachArray);

        return self::publish("oil.retail.direct","email",json_encode($data));
    }

    /**
     * 发送微信提醒
     * @param $userIds
     * @param $content
     * @return bool
     */
    public static function publishWinxinReminder($userIds,$content)
    {
        $data=array("userIds"=>$userIds,"content"=>$content);

        return self::publish("oil.retail.direct","weinxin.remind",json_encode($data));
    }

    /**
     * 发送微信提醒
     * @param $userIds
     * @param $title
     * @param $msg
     * @param $link
     * @return bool
     */
    public static function publishWinxinSingleNewsReminder($userIds,$title, $msg, $link)
    {
        $data=array("userIds"=>$userIds,"title"=>$title,"msg"=>$msg,"link"=>$link);

        return self::publish("oil.retail.direct","weinxin.singlenews.remind",json_encode($data));
    }


    /**
     * [publishSendSms 发布发送短信验证码事件]
     * @param
     * @param  string $phone [手机号]
     * @return bool
     */
    public static function publishSendCode($phone)
    {
        $data = array("phone"=>$phone);
        return self::publish("oil.retail.direct","sms.send.code",json_encode($data));
    }

    /**
     * 发布订单生效消息
     * @param $orderId
     * @return bool
     */
    public static function publishOrderEffected($orderId)
    {
        if (isset($orderId) && Utility::checkQueryId($orderId) && $orderId > 0){
            return self::publish('oil.retail.direct', static::OIL_RETAIL_ROUTING_KEY_ORDER_EFFECTED, $orderId);
        }
    }

    /**
     * 推送油站信息给财务系统
     * @param int $stationId
     * @return bool
     */
    public static function publishOilStationToFinanceSystem($stationId) {
        if (Utility::checkQueryId($stationId) && $stationId > 0){
            return self::publish('oil.retail.direct', static::QUEUE_OIL_STATION_TO_FINANCE_SYSTEM, $stationId);
        }
    }

    /**
     * 推送物流企业信息给财务系统
     * @param int $logisticsId
     * @return bool
     */
    public static function publishLogisticsCompanyToFinanceSystem($logisticsId) {
        if (Utility::checkQueryId($logisticsId) && $logisticsId > 0){
            return self::publish('oil.retail.direct', static::QUEUE_LOGISTICS_COMPANY_TO_FINANCE_SYSTEM, $logisticsId);
        }
    }
}