<?php

namespace ddd\OilStation\Domain\OilCompany;


use ddd\Infrastructure\DIService;

trait TraitOilCompanyRepository{

    private $oilCompanyRepository;

    /**
     * @return IOilCompanyRepository
     * @throws \Exception
     */
    public function getOilCompanyRepository(){
        if(empty($this->oilCompanyRepository)){
            $this->oilCompanyRepository = DIService::getRepository(IOilCompanyRepository::class);
        }
        return $this->oilCompanyRepository;
    }
}