<?php

namespace ddd\OilStation\Domain;


use ddd\Common\Domain\BaseEntity;

class Attachment extends BaseEntity{

    protected $id = 0;           //标志id
    protected $type = 0;
    protected $name = '';         //附件名称
    protected $path = '';
    protected $url = '';
    protected $status = 0;
    protected $remark = '';

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
    public function getPath():string{
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path):void{
        $this->path = $path;
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

    /**
     * @return int
     */
    public function getStatus():int{
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status):void{
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getRemark():string{
        return $this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark(string $remark):void{
        $this->remark = $remark;
    }

}