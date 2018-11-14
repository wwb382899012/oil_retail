<?php

namespace ddd\OilStation\Application;

use ddd\Common\Application\TransactionService;
use ddd\Infrastructure\error\ZEntityNotExistsException;
use ddd\OilStation\Domain\OilPrice\OilPrice;
use ddd\OilStation\Domain\OilPrice\TraitOilPriceRepository;
use ddd\OilStation\DTO\OilPrice\OilPriceDTO;

/**
 * 具备事务能力
 * Class OilPriceService
 * @package ddd\OilPrice\Application
 */
class OilPriceService extends TransactionService
{

    use TraitOilPriceRepository;

    public function assetDto(array $reqData): OilPriceDTO {
        $dto = new OilPriceDTO();
        $dto->setAttributes($reqData);
        return $dto;
    }

    /**
     * @param int $priceId
     * @return OilPriceDTO
     * @throws \Exception
     */
    public function getDetailDto(int $priceId): OilPriceDTO {
        $entity = $this->getEntityById($priceId);

        $dto = new OilPriceDTO();
        $dto->fromEntity($entity);

        return $dto;
    }

    /**
     * @param $priceId
     * @return OilPrice
     * @throws \Exception
     */
    public function getEntityById($priceId): OilPrice {
        $entity = $this->getOilPriceRepository()->findById($priceId);
        if (empty($entity)) {
            throw new ZEntityNotExistsException($priceId, OilPrice::class);
        }

        return $entity;
    }

    /**
     * @param OilPrice $entity
     * @return OilPrice
     * @throws \Exception
     */
    public function save(OilPrice $entity): OilPrice {
        try {
            $this->beginTransaction();

            $entity->save();

            $this->commitTransaction();

            return $entity;
        } catch (\Exception $e) {
            $this->rollbackTransaction();

            throw $e;
        }
    }

    /**
     * 获取油品当前可用价格
     * @param $stationId
     * @param $goodsId
     * @return OilPrice|null
     * @throws \Exception
     */
    public function getActivePriceByOilStationAndGoodsId($stationId, $goodsId): ?OilPrice {

        return $this->getOilPriceRepository()->findActivePriceByStationIdAndGoodsId($stationId, $goodsId);

        /*$oilPrices = $this->getOilPriceRepository()->findAllByStationIdAndGoodsId($stationId, $goodsId);
        if (\Utility::isNotEmpty($oilPrices)) {
            foreach ($oilPrices as $oilPrice) {
                if ($oilPrice->isActive()) {
                    return $oilPrice;
                    break;
                }
            }
        }
        return null;*/
    }

    public function getOilStationAllActivePrice($stationId)
    {
        return $this->getOilPriceRepository()->findAllActivePriceByStationId($stationId);
    }
}