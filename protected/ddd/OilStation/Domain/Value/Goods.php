<?php

namespace ddd\OilStation\Domain\Value;


use ddd\Common\Domain\BaseValue;

class Goods extends BaseValue{

    protected $id = 0;

    protected $name = '';

    public function __construct(int $id, string $name = "", array $params = null){
        parent::__construct($params);
        $this->id = $id;
        $this->name = $name;
    }

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

    public function __toString():string{
        return $this->name;
    }
}