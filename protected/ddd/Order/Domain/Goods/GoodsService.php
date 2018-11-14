<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/18 0018
 * Time: 14:37
 */

namespace app\ddd\Order\Domain\Goods;


use ddd\Common\Domain\BaseService;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\OilStation\Application\OilPriceService;
use ddd\OilStation\Domain\OilStation\IOilStationRepository;

class GoodsService extends BaseService
{
    /**
     * 获取油站所有可售油品
     * @param $stationId
     * @return array
     * @throws \Exception
     */
    public function getOilStationAllCanSellGoods($stationId)
    {
        if (empty($stationId))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'stationId']);
        }

        $stationEntity = DIService::getRepository(IOilStationRepository::class)->findById($stationId);
        if (empty($stationEntity))
        {
            ExceptionService::throwBusinessException(BusinessError::Oil_Station_Not_Exist, ['station_id' => $stationId]);
        }
        if (!$stationEntity->isActive())
        {
            ExceptionService::throwBusinessException(BusinessError::Oil_Station_Not_Active, ['station_id' => $stationId]);
        }

        $res = [];
        $prices = DIService::get(OilPriceService::class)->getOilStationAllActivePrice($stationId);
        if (\Utility::isNotEmpty($prices))
        {
            foreach ($prices as $index => $price)
            {
                $goods = new Goods();
                $goods->goods_id = $price->goods->getId();
                $goods->goods_name = $price->goods->getName();
                $goods->station_id = $stationId;
                $goods->price_buy = $price->getAgreedPrice();
                $goods->price_sell = $price->getDiscountPrice();
                $goods->price_retail = $price->getRetailPrice();
                if ($goods->isActive())
                {
                    $res[] = $goods;
                }
            }
        }

        return $res;
    }
}