<?php

namespace ddd\OilStation\Domain;


use ddd\Common\Domain\BaseEntity;

class AreaDictTree extends BaseEntity{

    protected $id = 0;

    protected $name = '';

    protected $children = [];

    /**
     * @return int
     */
    public function getId():int{
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id):void{
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName():string{
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name):void{
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getChildren():array{
        return $this->children;
    }

    /**
     * @param array $children
     */
    public function setChildren(array $children):void{
        $this->children = $children;
    }

    public function addChild(AreaDictTree $areaDictEntity){
        $this->children[$areaDictEntity->getId()] = $areaDictEntity;
    }

}