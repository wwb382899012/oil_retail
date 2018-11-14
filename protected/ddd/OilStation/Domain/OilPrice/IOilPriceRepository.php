<?php

namespace ddd\OilStation\Domain\OilPrice;

use ddd\Common\Domain\IRepository;

interface IOilPriceRepository extends IRepository{

    /**
     * 根据油站和商品查找第二天要生效的商品价格
     * @param int $stationId
     * @param int $goodsId
     * @return mixed
     */
    public function findPrepareEffectPriceByStationIdAndGoodsId(int $stationId,int $goodsId);

    /**
     * 根据油站和商品查找生效的商品价格
     * @param int $stationId
     * @param int $goodsId
     * @return OilPrice
     */
    public function findActivePriceByStationIdAndGoodsId(int $stationId,int $goodsId);

    /**
     * 根据油站查找生效的商品价格
     * @param int $stationId
     * @return OilPrice
     */
    public function findAllActivePriceByStationId(int $stationId);

    /**
     * 保存失效时间
     * @param OilPrice $oilPrice
     * @return mixed
     */
    public function saveEndTime(OilPrice $oilPrice);

    function findAllByStationId(int $stationId):array;

    function findAllByCompanyId(int $companyId):array;

    function findAllByStationIdAndGoodsId(int $stationId,int $goodsId):array;
}