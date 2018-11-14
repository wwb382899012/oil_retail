<?php

/**
 * Created by vector.
 * DateTime: 2018/9/11 11:58
 * Describe：
 */

class SmsService
{

    private static $service_map = [
        'sms'    => '/send/sms', //功能短信
        'mktsms' => '/send/mktsms', //营销短信
    ];

    public static $cacheKeyPrefix  = "oil_retail_sms_";
    public static $cacheCode = "code_";
    public static $cacheNum  = "num_";

    private static $mer_no               = "10007";
    private static $mer_key              = "3022CE05486B2484";
    private static $sms_code_expire_time = 300; //5分钟内有效
    private static $sms_tpl_ids      = [
        'send_code' => 101,
    ];
    private static $sms_send_max_time    = 5; //每天同一个手机号可以发送短信的最大次数

    /**
     * 调用接口命令
     * @param $params
     * @return mixed
     */
    public static function cmd($params)
    {
        $sms_url = Mod::app()->params["sms_url"];
        $url     = $sms_url.$params['cmd'];
        unset($params['cmd']);
        
        return Utility::cmd($params['data'],$url);
    }

    /**
     * 发送验证码短信
     * @param $phone
     * @return int
     */
    public static function sendCode($phone)
    {
        if(empty($phone))
            return false;

        $code = self::getCode($phone);

        $res = self::sendSms($phone, self::$sms_tpl_ids['send_code'], array('code'=>$code));
        if($res === true) {
            self::incr($phone);
        }
        return $res;
    }

    /**
     * 短信功能发送
     * @param $phone
     * @param $tplId
     * @param $tplParams
     * @return bool|void
     */
    public static function sendSms($phone, $tplId, $tplParams=array())
    {
        if (empty($phone) || empty($tplId) || empty($tplParams)) {
            return false;
        }

        $params=array(
            "mer_no"     => self::$mer_no,
            "tpl_id"     => $tplId,
            "phone"      => $phone,
            "tpl_params" => $tplParams
        );

        $params['sign'] = self::generateSign($params);

        $data = array(
            'cmd' => self::$service_map['sms'],
            'data' => $params
        );
        $res=self::cmd($data);
        if($res["code"]==0)
        {
            return true;
        }
        else
        {
            Mod::log("短信发送出错，参数：".json_encode($data)."，错误信息：".$res["msg"],"error");
            return false;
        }
    }


    /**
     * 发送营销短信
     * @param 
     * @return int
     */
    public static function sendMktSms()
    {
        return ;
    }


    /**
     * @desc 生成接口请求签名
     * @param array $params
     * @return string
     */
    public static function generateSign($params) {
        $strs = [];
        if (Utility::isNotEmpty($params)) {
            ksort($params);
            foreach ($params as $key => $value) {
                if(is_array($value))
                    $value = json_encode($value);
                $strs[] = $key . '=' . $value;
            }
        }

        $strs[] = 'mer_key='.self::$mer_key;

        $str = implode('&', $strs);

        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 需加密的参数串:' . $str);

        return strtoupper(md5($str));
    }


    public static function getCode($phone)
    {
        if(empty($phone))
            return false;

        $key  = static::$cacheKeyPrefix.static::$cacheCode.$phone;
        $code = Utility::getCache($key);
        if(!empty($code))
            return $code;

        $code = str_pad(random_int(1,9999),4,0,STR_PAD_LEFT);

        Utility::setCache($key, $code, self::$sms_code_expire_time);

        return $code;
    }

    public static function incr($phone)
    {
        if(empty($phone))
            return false;

        $keyName = static::$cacheKeyPrefix.static::$cacheNum.$phone;
        $redis   = Mod::app()->redis;

        $redis->incr($keyName);

        if($redis->ttl($keyName) == -1)
            $redis->expireAt($keyName, strtotime('+1 day'));
    }


    public static function isCanSendSms($phone)
    {
        if(empty($phone))
            return false;

        $keyName = static::$cacheKeyPrefix.static::$cacheNum.$phone;
        $res     = Utility::getCache($keyName);

        if($res > self::$sms_send_max_time)
            return false;

        return true;
    }

}