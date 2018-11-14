<?php

namespace ddd\OilStation\Domain\OilStation;


use ddd\Infrastructure\DIService;

Trait TraitOilStationRepository{

    private $oilStationRepository;

    /**
     * @return IOilStationRepository
     * @throws \Exception
     */
    protected function getOilStationRepository(){

        if(empty($this->oilStationRepository)){
            $this->oilStationRepository = DIService::getRepository(IOilStationRepository::class);
        }
        return $this->oilStationRepository;
    }
}