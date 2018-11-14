<?php

namespace ddd\OilStation\Domain\Value;


use ddd\Common\Domain\BaseValue;

class Area extends BaseValue{

    protected $code = 0;

    protected $name = '';

    public function __construct(int $code,string $name, array $params = null){
        parent::__construct($params);
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getCode():int{
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code):void{
        $this->code = $code;
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