<?php

namespace ddd\OilStation\Domain\Value;


use ddd\Common\Domain\BaseValue;

class Attachment extends BaseValue{

    protected $id = 0;           //标志id
    protected $type = 0;
    protected $name = '';         //附件名称
    protected $url = '';

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
     * @return int
     */
    public function getType():int{
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type):void{
        $this->type = $type;
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
     * @return string
     */
    public function getUrl():string{
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url):void{
        $this->url = $url;
    }

}