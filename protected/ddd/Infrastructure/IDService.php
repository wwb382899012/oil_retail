<?php
/**
 * Desc: 获取统一编号的服务类，日期字符+顺序号：201611020001
 * User: susiehuang
 * Date: 2018/9/5 0005
 * Time: 9:44
 */

namespace ddd\Infrastructure;


class IDService
{
    /**
     * 获取物流企业限额编号id
     * @return int|string
     */
    public static function getLogisticsQuotaLimitCodeId()
    {
        return self::getId("logistics.quota.limit.code.id.", 2);
    }

    /**
     * 获取车辆限额编号id
     * @return int|string
     */
    public static function getVehicleQuotaLimitCodeId()
    {
        return self::getId("vehicle.quota.limit.code.id.", 2);
    }

    /**
     * 获取订单编号
     * @return int|string
     */
    public static function getOrderCodeId()
    {
        return self::getId("oil.retail.order.id.", 6);
    }

    /**
     * 获取价格申请编号
     * @return string
     */
    public static function getOilPriceApplyCode():string {
        return 'JG'.self::getId("oil.retail.oil.price.id.", 4);
    }

    /**
     * 获取指定Key的当天的日期及顺序号的组合ID
     * @param $key
     * @param int $len 顺序号的长度，默认为6
     * @param string $dateFormat 获取时间的格式化字符串
     * @param int $expire 过期时间
     * @param string $date 日期
     * @return int|string
     */
    public static function getId($key, $len = 6, $dateFormat = "Ymd", $expire = 86400, $date = '')
    {
        if (empty($date))
        {
            $date = date($dateFormat);
        }
        $keyName = $key . $date;

        $redis = \Mod::app()->redis;

        $id = $redis->incr($keyName);
        if ($id < 1)
        {
            \Mod::log("获取Key为：" . $key . "的id出错");

            return 0;
        }
        if ($id == 1)
        {
            $redis->expire($keyName, $expire);
        }

        $id = "000000000000000000" . $id;

        $id = substr($id, strlen($id) - $len);

        return $date . $id;
    }

    /**
     * @desc 获取自然序列号
     * @param string $key
     * @return string
     */
    public static function getSerialNum($key)
    {
        $redis = \Mod::app()->redis;
        $serial = $redis->incr($key);
        if ($serial < 1)
        {
            \Mod::log("获取Key为：" . $key . "的序列号出错", \CLogger::LEVEL_ERROR);
            $serial = 0;
        }

        return $serial;
    }
}