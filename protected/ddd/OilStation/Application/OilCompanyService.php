<?php

namespace ddd\OilStation\Application;

use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ZException;
use ddd\OilStation\Domain\OilCompany\OilCompany;
use ddd\OilStation\Domain\OilCompany\TraitOilCompanyRepository;
use ddd\OilStation\DTO\OilCompany\OilCompanyDTO;

/**
 * ，具备事务能力
 * Class OilCompanyService
 * @package ddd\OilStation\Application
 */
class OilCompanyService extends OilService{

    use TraitOilCompanyRepository;

    public function assetDto(array $reqData):OilCompanyDTO{
        $dto = new OilCompanyDTO();
        $dto->setAttributes($reqData);
        $dto->name = trim($dto->name);
        $dto->short_name = trim($dto->short_name);
        $dto->ownership = (int) $dto->ownership;
        $dto->files = $this->getFileDtos($reqData);

        return $dto;
    }

    public function getDetailDto(int $companyId):OilCompanyDTO{
        $entity = $this->getEntityById($companyId);

        $dto = new OilCompanyDTO();
        $dto->fromEntity($entity);

        return $dto;
    }

    public function getEntityById($companyId){
        $entity = $this->getOilCompanyRepository()->findById($companyId);
        if(empty($entity)){
            throw new ZException(BusinessError::ENTITY_NOT_EXISTS);
        }

        return $entity;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAllCompanyIdNames():array {
        $idNames = $this->getOilCompanyRepository()->getAllCompanyIdNames();
        if(\CheckUtility::isEmpty($idNames)){
            return [];
        }

        $data = [];
        foreach($idNames as $id => $name){
            $data[] = [
                'id' => $id,
                'value'=>$name,
            ];
        }

        return $data;
    }

    public function save(OilCompany $entity):OilCompany{
        try{
            $this->beginTransaction();

            $entity->save();

            $this->commitTransaction();

            return $entity;
        }catch(\Exception $e){
            $this->rollbackTransaction();

            throw $e;
        }
    }
}