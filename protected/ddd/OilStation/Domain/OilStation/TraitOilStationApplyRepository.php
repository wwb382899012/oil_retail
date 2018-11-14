<?php

namespace ddd\OilStation\Domain\OilStation;


use ddd\Infrastructure\DIService;

Trait TraitOilStationApplyRepository{

    private $oilStationApplyRepository;

    /**
     * @return IOilStationApplyRepository
     * @throws \Exception
     */
    protected function getOilStationApplyRepository(){
        if(empty($this->oilStationApplyRepository)){
            $this->oilStationApplyRepository = DIService::getRepository(IOilStationApplyRepository::class);
        }
        return $this->oilStationApplyRepository;
    }
}