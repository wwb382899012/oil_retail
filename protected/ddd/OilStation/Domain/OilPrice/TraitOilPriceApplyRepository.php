<?php

namespace ddd\OilStation\Domain\OilPrice;


use ddd\Infrastructure\DIService;

Trait TraitOilPriceApplyRepository{

    private $oilPriceApplyRepository;

    /**
     * @return IOilPriceApplyRepository
     * @throws \Exception
     */
    protected function getOilPriceApplyRepository(){

        if(empty($this->oilPriceApplyRepository)){
            $this->oilPriceApplyRepository = DIService::getRepository(IOilPriceApplyRepository::class);
        }
        return $this->oilPriceApplyRepository;
    }
}