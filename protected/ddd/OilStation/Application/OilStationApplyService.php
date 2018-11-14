<?php

namespace ddd\OilStation\Application;

use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ZException;
use ddd\OilStation\Domain\OilStation\OilStationApply;
use ddd\OilStation\Domain\OilStation\OilStationApplyEnum;
use ddd\OilStation\Domain\OilStation\TraitOilStationApplyRepository;
use ddd\OilStation\DTO\OilStation\OilStationApplyDTO;

/**
 * 具备事务能力
 * Class OilStationApplyService
 * @package ddd\OilStationApply\Application
 */
class OilStationApplyService extends OilService{

    use TraitOilStationApplyRepository;

    public function assetDto(array $reqData):OilStationApplyDTO{
        $dto = new OilStationApplyDTO();
        $dto->setAttributes($reqData);
        $dto->name = trim($dto->name);
        $dto->files = $this->getFileDtos($reqData);

        return $dto;
    }

    /**
     * @param int $applyId
     * @return OilStationApplyDTO
     * @throws \Exception
     */
    public function getDetailDto(int $applyId):OilStationApplyDTO{
        $entity = $this->getEntityById($applyId);

        $dto = new OilStationApplyDTO();
        $dto->fromEntity($entity);

        return $dto;
    }

    /**
     * @param $applyId
     * @return OilStationApply
     * @throws \Exception
     */
    public function getEntityById($applyId):OilStationApply {
        $entity = $this->getOilStationApplyRepository()->findById($applyId);
        if(empty($entity)){
            throw new ZException(BusinessError::ENTITY_NOT_EXISTS);
        }

        return $entity;
    }

    /**
     * @param int    $applyId
     * @param string $name
     * @return bool
     * @throws \Exception
     */
    public function checkNameIsExist(int $applyId,string $name):bool {
        $entity = $this->getOilStationApplyRepository()->find('t.apply_id <> :apply_id AND t.name = :name AND t.status = :status',[
            'apply_id' => $applyId,
            'name' => $name,
            'status' => OilStationApplyEnum::STATUS_PASSED,
        ]);

        if(!empty($entity)){
            return true;
        }

        return false;
    }

    /**
     * @param OilStationApply $entity
     * @param bool            $isSubmit
     * @return OilStationApply
     * @throws \Exception
     */
    public function save(OilStationApply $entity,bool $isSubmit = false):OilStationApply{
        try{
            $this->beginTransaction();

            $entity->save();

            if($isSubmit){
                $entity->setIsSubmitted();
            }

            $this->commitTransaction();

            return $entity;
        }catch(\Exception $e){
            $this->rollbackTransaction();

            throw $e;
        }
    }

    public function setIsSubmitted(OilStationApply $entity){
        try{
            $this->beginTransaction();

            if(!$entity->isCanEdit()){
                throw new ZException(BusinessError::Oil_StationApply_Not_Allow_Submit);
            }

            $entity->setIsSubmitted();

            $this->commitTransaction();

            return $entity;
        }catch(\Exception $e){
            $this->rollbackTransaction();

            throw $e;
        }
    }

    public function isCanEdit(int $status):bool{
        return $status < OilStationApplyEnum::STATUS_SUBMIT;
    }
}