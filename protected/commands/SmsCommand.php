<?php
/**
 * Created by vector.
 * DateTime: 2018/9/11 14:24
 * Describe：
 */

class SmsCommand extends AMQPCommand
{
    /**
     * 需要监听的队列信息
     * @var array
     */
    protected $queueConfig = array(
        "oil.retail.sms.send.code"=>array(
            "fn"=>"sendCode",
            "exchange"=>"oil.retail.direct",
            "routingKey"=>"sms.send.code",
        ),
    );

    public function init()
    {
        $this->sleepTime = 1;
        $this->maxTaskPerChild=2000;
        parent::init();
    }


    /**
     * 发送短信验证码
     * @param $msg
     * @throws Exception
     */
    public function sendCode($msg)
    {
        $params = json_decode($msg, true);
        if (empty($params["phone"]))
            return false;

        SmsService::sendCode($params['phone']);
    }

}