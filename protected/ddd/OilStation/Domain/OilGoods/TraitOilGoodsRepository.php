<?php

namespace ddd\OilStation\Domain\OilGoods;


use ddd\Infrastructure\DIService;

Trait TraitOilGoodsRepository{

    private $oilGoodsRepository;

    /**
     * @return IOilGoodsRepository|object
     * @throws \Exception
     */
    public function getOilGoodsRepository(){

        if(empty($this->oilGoodsRepository)){
            $this->oilGoodsRepository = DIService::getRepository(IOilGoodsRepository::class);
        }
        return $this->oilGoodsRepository;
    }
}