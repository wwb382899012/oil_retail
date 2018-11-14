<?php

namespace ddd\OilStation\DTO;


use ddd\Common\Application\BaseDTO;

class AreaDictDto extends BaseDTO{

    public $id = 0;

    public $name = '';

    public $children = [];

    public function fromEntity($tree){
        $this->id = $tree->id;
        $this->name = $tree->name;
        $this->children = [];

        $children = $tree->getChildren();
        if (\CheckUtility::isNotEmpty($children)) {
            foreach ($children as $child) {
                $childDTO = new AreaDictDto();
                $childDTO->fromEntity($child);
                $this->children[] = $childDTO;
            }
        }
    }
}