<?php

namespace ddd\OilStation\Application;

use ddd\Common\Application\TransactionService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ZException;
use ddd\OilStation\Domain\OilGoods\OilGoods;
use ddd\OilStation\Domain\OilGoods\TraitOilGoodsRepository;
use ddd\OilStation\DTO\OilGoods\OilGoodsDTO;

/**
 * 具备事务能力
 * Class OilGoodsService
 * @package ddd\OilStation\Application
 */
class OilGoodsService extends TransactionService{

    use TraitOilGoodsRepository;

    public function assetDto(array $reqData):OilGoodsDTO{
        $dto = new OilGoodsDTO();
        $dto->setAttributes($reqData);
        $dto->name = trim($dto->name);
		$dto->sort = (int) $dto->sort;
        return $dto;
    }

    /**
     * @param int $goodsId
     * @return OilGoodsDTO
     * @throws ZException
     */
    public function getDetailDto(int $goodsId):OilGoodsDTO{
        $entity = $this->getEntityById($goodsId);

        $dto = new OilGoodsDTO();
        $dto->fromEntity($entity);

        return $dto;
    }

    /**
     * @param $goodsId
     * @return OilGoods
     * @throws ZException
     */
    public function getEntityById($goodsId):OilGoods {
        $entity = $this->getOilGoodsRepository()->findById($goodsId);
        if(empty($entity)){
            throw new ZException(BusinessError::ENTITY_NOT_EXISTS);
        }

        return $entity;
    }

    /**
     * @param OilGoods $entity
     * @return OilGoods
     * @throws \Exception
     */
    public function save(OilGoods $entity):OilGoods{
        try{
            $this->beginTransaction();

            $entity->save();

            $this->commitTransaction();

            return $entity;
        }catch(\Exception $e){
            $this->rollbackTransaction();

            throw $e;
        }
    }
}