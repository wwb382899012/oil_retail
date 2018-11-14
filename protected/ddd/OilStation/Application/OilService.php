<?php

namespace ddd\OilStation\Application;

use ddd\Common\Application\TransactionService;
use ddd\OilStation\DTO\AttachmentDTO;

abstract class OilService extends TransactionService{

    /**
     * @param array $reqData
     * @return array
     * @throws \Exception
     */
    protected function getFileDtos(array $reqData){
        if(!isset($reqData['files']) || \Utility::isEmpty($reqData['files'])){
            return [];
        }

        $files = [];
        foreach($reqData['files'] as $file){
            $fileDto = new AttachmentDTO();
            $fileDto->setAttributes($file);
            $files[] = $fileDto;
        }

        return $files;
    }
}