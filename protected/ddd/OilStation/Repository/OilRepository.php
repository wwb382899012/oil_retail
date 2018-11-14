<?php

namespace ddd\OilStation\Repository;

use app\ddd\Common\Domain\Value\Operator;
use ddd\Common\Domain\Value\DateTime;
use ddd\Common\Repository\EntityRepository;
use ddd\Infrastructure\error\ZModelDeleteFalseException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\OilStation\Domain\Attachment;
use ddd\OilStation\Domain\OilCommonEntity;

abstract class OilRepository extends EntityRepository{

    /**
     * @param OilCommonEntity $entity
     * @param \CActiveRecord  $model
     */
    protected function setCommonAttributes(OilCommonEntity & $entity,\CActiveRecord & $model):void {
        $entity->setRemark($model->remark);
        $entity->setEffectTime(new DateTime($model->effect_time));
        $entity->setCreateTime(new DateTime($model->create_time));
        $entity->setUpdateTime(new DateTime($model->update_time));
        $entity->setCreateUser(new Operator($model->create_user_id,$model->createUser->name));
        $entity->setUpdateUser(new Operator($model->update_user_id,$model->updateUser->name));
    }

    /**
     * @param \CActiveRecord $model
     * @return array
     */
    protected function getFileEntities(\CActiveRecord $model):array {
        if(!isset($model->files) || \Utility::isEmpty($model->files)){
            return [];
        }

        $files = [];
        foreach($model->files as $fileModel){
            $files[$fileModel->id] = $this->getFileEntity($fileModel);
        }

        return $files;
    }

    /**
     * @param \CActiveRecord $fileModel
     * @return Attachment
     */
    protected function getFileEntity(\CActiveRecord $fileModel):Attachment{
        $entity =  new Attachment();
        $entity->setId($fileModel->id);
        $entity->setType($fileModel->type);
        $entity->setName($fileModel->name);
        $entity->setUrl($fileModel->file_url);
        $entity->setPath($fileModel->file_path);
        $entity->setRemark($fileModel->remark);
        $entity->setStatus($fileModel->status);

        return $entity;
    }

    /**
     * @param string         $className
     * @param \CActiveRecord $model
     * @param array          $files
     * @throws ZModelDeleteFalseException
     * @throws ZModelSaveFalseException
     * @throws \CDbException
     * @throws \CException
     */
    protected function saveFiles(string $className,\CActiveRecord $model,array $files):void {
        if(\Utility::isEmpty($files)){
            return;
        }

        $needDeleteFileModels = [];
        if(isset($model->files) && \CheckUtility::isNotEmpty($model->files)){
            foreach($model->files as & $fileModel){
                $needDeleteFileModels[$fileModel->id] = $fileModel;
            }
        }

        foreach($files as & $fileEntity){
            $id = $this->saveFile($className,$model->getPrimaryKey(),$fileEntity);
            unset($needDeleteFileModels[$id]);
        }

        if(\CheckUtility::isNotEmpty($needDeleteFileModels)){
            foreach($needDeleteFileModels as $deleteFileModel){
                $deleteFileModel->status = 0;
                if(!$deleteFileModel->save()){
                    throw new ZModelDeleteFalseException($deleteFileModel);
                }
            }
        }
    }

    /**
     * @param string     $className
     * @param int        $baseId
     * @param Attachment $entity
     * @return int
     * @throws ZModelSaveFalseException
     * @throws \CDbException
     * @throws \CException
     */
    private function saveFile(string $className,int $baseId,Attachment $entity):int{
        $model = $this->getModel($className);
        $file_model = $model->findByPk($entity->getId());
        if(empty($file_model)){
            $file_model = $model;
            $file_model->setIsNewRecord(true);
        }

        $file_model->id = $entity->getId();
        $file_model->base_id = $baseId;
        $file_model->type = $entity->getType();
        $file_model->status = 1;
        $file_model->remark = '';

        if(!$file_model->save()){
            throw new ZModelSaveFalseException($file_model);
        }

        return $file_model->getPrimaryKey();
    }

    /**
     * @param $className
     * @return \CActiveRecord
     */
    private function getModel($className):\CActiveRecord{
        return new $className(null);
    }
}