<?php

namespace ddd\OilStation\DTO;


use ddd\Common\Application\BaseDTO;
use ddd\OilStation\Domain\Attachment;

class AttachmentDTO extends BaseDTO{

    public $id;           //标志id
    public $type;
    public $name;         //附件名称
    public $url;     //文件路径

    public function rules(){
        return [
            ["id", "required", "message" => "信息异常，缺少必要参数文件id！"],
            ["type", "required", "message" => "信息异常，缺少必要参数附件类型！"],
            ["id", "numerical", "integerOnly" => true, "min" => 1, "tooSmall" => "信息异常，缺少必要参数文件id！"],
            ["type", "numerical", "integerOnly" => true, "min" => 1, "tooSmall" => "信息异常，缺少必要参数附件类型！"],
        ];
    }

    /**
     * 从实体对象生成DTO对象
     * @param Attachment $entity
     * @throws \Exception
     */
    public function fromEntity(Attachment $entity){
        $this->id = $entity->getId();
        $this->type = $entity->getType();
        $this->name = $entity->getName();
        $this->url = $entity->getUrl();
    }

    /**
     * 转换成实体对象
     * @return Attachment
     */
    public function toEntity(){
        $entity =  new Attachment();
        $entity->id = $this->id;
        $entity->type = $this->type;
        $entity->name = $this->name;
        $entity->url = $this->url;

        return $entity;
    }
}