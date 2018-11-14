<?php

namespace ddd\OilStation\Domain\OilPrice;


use ddd\Infrastructure\DIService;

Trait TraitOilPriceRepository{

    private $oilPriceRepository;

    /**
     * @return IOilPriceRepository
     * @throws \Exception
     */
    public function getOilPriceRepository(){

        if(empty($this->oilPriceRepository)){
            $this->oilPriceRepository = DIService::getRepository(IOilPriceRepository::class);
        }
        return $this->oilPriceRepository;
    }
}