<?php

namespace ddd\OilStation\Domain\Value;


use ddd\Common\Domain\BaseValue;

class Contact extends BaseValue{

    protected $name;

    protected $mobile;

    public function __construct(string $name, $mobile, array $params = null){
        parent::__construct($params);
        $this->name = $name;
        $this->mobile = $mobile;
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
     * @return mixed
     */
    public function getMobile(){
        return $this->mobile;
    }

    /**
     * @param mixed $mobile
     */
    public function setMobile($mobile):void{
        $this->mobile = $mobile;
    }

}