<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/18 0018
 * Time: 14:25
 */

namespace ddd\Order\Application;


use ddd\Common\Application\BaseService;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\ZException;

class GoodsService extends BaseService
{
    /**
     * 获取油站所有可售油品
     * @param $stationId
     * @return array
     * @throws ZException
     */
    public function getOilStationAllCanSellGoods($stationId)
    {
        try
        {
            return DIService::get(\app\ddd\Order\Domain\Goods\GoodsService::class)->getOilStationAllCanSellGoods($stationId);
        } catch (\Exception $e)
        {
            throw new ZException($e->getMessage(), $e->getCode());
        }
    }
}