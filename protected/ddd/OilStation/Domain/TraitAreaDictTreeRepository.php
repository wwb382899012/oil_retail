<?php

namespace ddd\OilStation\Domain;


use ddd\Infrastructure\DIService;

trait TraitAreaDictTreeRepository{

    private $areaDictRepository;

    /**
     * @return IAreaDictTreeRepository
     * @throws \Exception
     */
    public function getAreaDictRepository():IAreaDictTreeRepository{
        if(empty($this->areaDictRepository)){
            $this->areaDictRepository = DIService::getRepository(IAreaDictTreeRepository::class);
        }
        return $this->areaDictRepository;
    }
}