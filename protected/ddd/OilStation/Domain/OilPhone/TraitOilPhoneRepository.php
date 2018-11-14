<?php

namespace ddd\OilStation\Domain\OilPhone;


use ddd\Infrastructure\DIService;

Trait TraitOilPhoneRepository{

    private $oilPhoneRepository;

    /**
     * @return IOilPhoneRepository
     * @throws \Exception
     */
    public function getOilPhoneRepository(){

        if(empty($this->oilPhoneRepository)){
            $this->oilPhoneRepository = DIService::getRepository(IOilPhoneRepository::class);
        }
        return $this->oilPhoneRepository;
    }
}