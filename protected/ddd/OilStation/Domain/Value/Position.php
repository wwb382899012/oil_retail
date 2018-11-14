<?php

namespace ddd\OilStation\Domain\Value;


use ddd\Common\Domain\BaseValue;

class Position extends BaseValue{

    protected $longitude = '';

    protected $latitude = '';

    public function __construct(string $longitude, string $latitude, array $params = null){
        parent::__construct($params);
        $this->longitude = $longitude;
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude():float{
        return (float) $this->longitude;
    }

    /**
     * @param string $longitude
     */
    public function setLongitude(string $longitude):void{
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getLatitude():float{
        return (float) $this->latitude;
    }

    /**
     * @param string $latitude
     */
    public function setLatitude(string $latitude):void{
        $this->latitude = $latitude;
    }

    public function __toString():string{
        return $this->longitude.'/'.$this->latitude;
    }
}