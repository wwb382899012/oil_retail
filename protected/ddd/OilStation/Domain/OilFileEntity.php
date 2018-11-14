<?php

namespace ddd\OilStation\Domain;


abstract class  OilFileEntity extends OilCommonEntity{

    /**
     * 附件
     * @var   array
     */
    protected $files = [];

    /**
     * 获取附件
     * @return   array
     */
    public function getFiles():array {
        return $this->files;
    }

    public function setFiles(array $files){
        $this->files = $files;
    }

    /**
     * 添加附件
     * @param Attachment $fileEntity
     */
    public function addFile(Attachment $fileEntity):void{
        $this->files[$fileEntity->getId()] = $fileEntity;
    }

    /**
     * 移除附件
     * @param    int $id
     */
    public function removeFile($id):void{
        unset($this->files[$id]);
    }

    /**
     * 清空附件
     */
    public function clearFiles():void{
        $this->files = [];
    }
}