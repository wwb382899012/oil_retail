<?php

namespace ddd\OilStation\DTO;

use ddd\Common\Domain\BaseEntity;
use ddd\OilStation\Domain\OilFileEntity;

abstract class OilFileDto extends OilCommonDto{

    public $files = [];

    public function fromEntity(BaseEntity $entity):void {
        $this->entityToDto($entity);
    }

    /**
     * @param OilFileEntity $entity
     * @throws \Exception
     */
    private function entityToDto(OilFileEntity $entity):void {
        $this->files = [];

        $files = $entity->getFiles();
        if(\Utility::isNotEmpty($files)){
            foreach($files as & $fileEntity){
                $fileDto = new AttachmentDTO();
                $fileDto->fromEntity($fileEntity);
                $this->files[] = $fileDto;
            }
        }

        parent::fromEntity($entity);
    }

    /**
     * @param OilFileEntity $entity
     */
    protected function setFilesToEntity(OilFileEntity & $entity):void {
        if(\Utility::isEmpty($this->files)){
            return;
        }

        foreach($this->files as & $fileDto){
            $entity->addFile($fileDto->toEntity());
        }
    }
}