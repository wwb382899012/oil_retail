<?php

namespace ddd\OilStation\Application;

use ddd\Common\Application\BaseService;
use ddd\OilStation\Domain\TraitAreaDictTreeRepository;
use ddd\OilStation\DTO\AreaDictDto;

class AreaDictTreeService extends BaseService{

    use TraitAreaDictTreeRepository;

    /**
     * @return AreaDictDto
     * @throws \Exception
     */
    public function getAreaTreeDto():AreaDictDto{
        $tree = $this->getAreaDictRepository()->getTree();

        $dto = new AreaDictDto();
        $dto->fromEntity($tree);
        return $dto;
    }
}