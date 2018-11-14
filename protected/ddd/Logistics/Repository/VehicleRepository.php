<?php
/**
 * Desc:
 * User: vector
 * Date: 2018/9/6
 * Time: 17:29
 */

namespace ddd\Logistics\Repository;

use app\ddd\Admin\Application\User\UserService;
use app\ddd\Common\Domain\Value\LogisticsCompany;
use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use app\ddd\Common\Repository\RedisHashCache;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;

use ddd\Common\Repository\EntityRepository;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\ZException;
use ddd\Infrastructure\error\ZModelDeleteFalseException;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\Infrastructure\Utility;
use ddd\Logistics\Domain\Vehicle\DrivingLicense;
use ddd\Logistics\Domain\Vehicle\IVehicleRepository;
use ddd\Logistics\Domain\Vehicle\Vehicle;


class VehicleRepository extends EntityRepository implements IVehicleRepository
{
	use RedisHashCache;

    public  $cacheAll    = '_all';
    public  $cacheActive = '_active';

	public function init()
    {
        $this->with=["logisticsCompany","photos"];
    }

	public function getNewEntity()
    {
        return new Vehicle();
    }

    public function getActiveRecordClassName()
    {
        return 'Vehicle';
    }


	public function findById($vehicleId)
    {
        if(empty($vehicleId))
            return null;
        
        $entity = $this->getEntityFromCache($vehicleId, $this->cacheAll);

    	if(!empty($entity))
    		return $entity;

    	$entity = parent::findById($vehicleId);
        if (!empty($entity))
        {
        	$this->setCache($vehicleId, $entity, $this->cacheAll);
        }

        return $entity;

    }


    /**
     * dataToEntity 将数据模型转换为实体
     * @param \CActiveRecord $model
     * @return BaseEntity|Vehicle
     * @throws \Exception
     */
	public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $values = $model->getAttributes(['number','logistics_id','model','optor','capacity','remark']);
        $entity->setId($model->vehicle_id);
        $entity->setAttributes($values);
        $entity->setStatus(new Status($model->status, $model->status_time));
        $entity->company = new LogisticsCompany($model->logistics_id, $model->logisticsCompany->name);
        if(!empty($model->effect_time))
            $entity->effect_time = new DateTime($model->effect_time);
        if(!empty($model->create_time))
            $entity->create_time = new DateTime($model->create_time);
        $createName = "";
        if(!empty($model->create_user_id))
            $createName = DIService::get(UserService::class)->getUser($model->create_user_id)->name;
        $entity->create_user = new Operator($model->create_user_id, $createName);
        if(!empty($model->update_time))
            $entity->update_time = new DateTime($model->update_time);
        $updateName = "";
        if(!empty($model->update_user_id))
            $updateName = DIService::get(UserService::class)->getUser($model->update_user_id)->name;
        $entity->update_user = new Operator($model->update_user_id, $updateName);
        $drivingLicense = DrivingLicense::create($model->start_date, $model->end_date, $model->photos);
        $entity->addDrivingLicense($drivingLicense);

        return $entity;
    }


    /**
     * 把对象持久化到数据
     * @param Vehicle $entity
     * @return Vehicle
     * @throws \Exception
     */
    public function store($entity)
    {
        $id = $entity->getId();
        $this->activeRecordClassName = $this->getActiveRecordClassName();
        $model = new $this->activeRecordClassName;
        
        if (!empty($id))
        {
            $model = $model::model()->findByPk($id);
            if (empty($model))
            {
                throw new ZModelNotExistsException($id, $this->getActiveRecordClassName());
            }
        }
        
        $values = $entity->getAttributes(['number','model','optor','capacity']);
        $model->setAttributes($values);
        $model->logistics_id = $entity->company->id;
        $model->remark = "添加车辆信息";
        $statusEntity = $entity->getStatus();
        if (!empty($statusEntity))
        {
			$model->status      = $statusEntity->status;
			$model->status_time = $statusEntity->status_time;
        }

        $drivingLicense = $entity->driving_license;
        if(!empty($drivingLicense)){
        	if (!empty($drivingLicense->start_date))
        		$model->start_date = $drivingLicense->start_date->toDate();
        	if (!empty($drivingLicense->end_date))
        		$model->end_date = $drivingLicense->end_date->toDate();
        }

        $isNew = $model->isNewRecord;

        $attachments = $drivingLicense->photos;
        if(empty($attachments))
        	$attachments = array();

        $res = $model->save();
        if ($res !== true)
	        throw new ZModelSaveFalseException($model);

	    $entity->setId($model->getPrimaryKey());

        if (!$isNew)
        {
            if(empty($attachments)){
                if (is_array($model->photos) && !empty($model->photos)){
                    foreach ($model->photos as $photo) {
                        $res = $photo->delete();
                        if (!$res)
                            throw new ZModelDeleteFalseException($photo);
                    }
                }
            }else{
                if (is_array($model->photos) && !empty($model->photos))
                {
                    foreach ($model->photos as $photo)
                    {
                        $attachment = $attachments[$photo->id];
                        if(empty($attachment)){
                            $res = $photo->delete();
                            if (!$res)
                                throw new ZModelDeleteFalseException($photo);

                            continue;
                        }
                        
                        $photo->file_url  = $attachment->url;
                        $photo->file_path = $attachment->url;
    
                        $res = $photo->save();
                        if (!$res)
                            throw new ZModelSaveFalseException($photo);
    
                        unset($attachments[$photo->id]);
                    }
                }
            }
        }

        if (is_array($attachments) && count($attachments) > 0)
        {
            foreach ($attachments as $attachment)
            {
            	$photo = new \PhotoAttachment();
                $photo->base_id   = $entity->getId();
                $photo->out_id    = $attachment->id; 
                $photo->type      = \PhotoAttachment::VEHICLE_PHOTO_TYPE;
                $photo->status    = \PhotoAttachment::EFFECTIVE_STATUS;
                $photo->file_url  = $attachment->url;
                $photo->file_path = $attachment->url;

				$res = $photo->save();
                if (!$res)
                    throw new ZModelSaveFalseException($photo);
            }
        }

        if($statusEntity->status == \Vehicle::PASS_STATUS){
       		$this->setCache($entity->getId(), $entity, $this->cacheActive);
        }else{
        	
        }

        // $this->clearCache($entity->getId(), $this->cacheActive);
        
        $this->clearCache($entity->getId(), $this->cacheAll);

        return $entity;
    }


    /**
     * @desc 更新状态
     * @param   Vehicle $entity
     * @throws  \Exception
     */
    public function updateStatus(Vehicle $entity)
    {
        if (empty($entity))
        {
            throw new ZException("Vehicle对象不存在");
        }
        $this->activeRecordClassName = $this->getActiveRecordClassName();
		$modelObj = new $this->activeRecordClassName;
		$model    = $modelObj::model()->findByPk($entity->getId());
        if (empty($model))
        {
            throw new ZModelNotExistsException($entity->getId(), \Utility::getClassBaseName($entity));
        }
        $statusEntity = $entity->getStatus();
        if ($model->status != $statusEntity->status)
        {
			$model->status         = $statusEntity->status;
			$model->status_time    = $statusEntity->status_time;
			$model->update_user_id = \Utility::getNowUserId();
			$model->update_time    = Utility::getNow();
            $res = $model->save();
            if ($res !== true)
            {
                throw new ZModelSaveFalseException($model);
            }

        	// $this->clearCache($entity->getId(), $this->cacheActive);
        	
        	$this->clearCache($entity->getId(), $this->cacheAll);
        }
    }


    /**
     * [getVehicleIdByNumber 根据车牌号获取车辆id]
     * @param
     * @param  [string] $number [车牌号]
     * @return [int]
     */
    public function getVehicleIdByNumber($number)
    {
        $vehicleId = 0;
        if(empty($number))
            throw new ZException("参数有误");

        $keyName    = $this->getCacheKey('_'.$number);
        $vehicleId = \Utility::getCache($keyName);

        if(!empty($vehicleId))
            return $vehicleId;

        $vehicle = \Vehicle::model()->find("number='".$number."'");
        if(!empty($vehicle)){
            $vehicleId = $vehicle->vehicle_id;
            \Utility::setCache($keyName, $vehicleId);
        }
        return $vehicleId;
    }


	/**
     * [clearCache 清除缓存]
     */
    public function clearCache($number="")
    {
        \Utility::clearCache($this->getCacheKey($this->cacheAll));

        if(!empty($number))
            \Utility::clearCache($this->getCacheKey('_'.$number));
    }
}