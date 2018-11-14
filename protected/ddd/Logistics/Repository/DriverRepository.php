<?php
/**
 * Desc:
 * User: vector
 * Date: 2018/9/6
 * Time: 17:29
 */

namespace ddd\Logistics\Repository;

use app\ddd\Admin\Application\User\UserService;
use app\ddd\Common\Domain\Value\Attachment;
use app\ddd\Common\Domain\Value\LogisticsCompany;
use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use app\ddd\Common\Domain\Value\Vehicle;
use app\ddd\Common\Repository\RedisHashCache;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\Common\Repository\EntityRepository;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\Utility;
use ddd\Infrastructure\error\ZException;
use ddd\Infrastructure\error\ZModelDeleteFalseException;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\Logistics\Domain\Driver\Driver;
use ddd\Logistics\Domain\Driver\IDriverRepository;

class DriverRepository extends EntityRepository implements IDriverRepository
{
	use RedisHashCache;

    public  $cacheAll    = '_all';
    public  $cacheActive = '_active';

	public function init()
    {
        $this->with=["logisticsCompany","vehicles","photos"];
    }

	public function getNewEntity()
    {
        return new Driver();
    }

    public function getActiveRecordClassName()
    {
        return 'Driver';
    }



	public function findById($customerId)
    {
    	$entity = "";
        if(empty($customerId))
            return $entity;

        $entity = $this->getEntityFromCache($customerId, $this->cacheAll);

    	if(!empty($entity))
    		return $entity;

    	$entity = $this->find("t.customer_id=".$customerId);
        if (!empty($entity))
        {
        	$this->setCache($customerId, $entity, $this->cacheAll);
        }

        return $entity;

    }



	/**
	 * [dataToEntity 将数据模型转换为实体]
	 * @param
	 * @param  [type] $model [description]
	 * @return [type]
	 */
	public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $values = $model->getAttributes(['name','customer_id','password','phone','remark']);
        $entity->setId($model->driver_id);
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
        if(empty($model->update_time))
            $entity->update_time = new DateTime($model->update_time);
        $updateName = "";
        if(!empty($model->update_user_id))
            $updateName = DIService::get(UserService::class)->getUser($model->update_user_id)->name;
        $entity->update_user = new Operator($model->update_user_id, $updateName);
       	if(!empty($model->vehicles) && is_array($model->vehicles)){
       		foreach ($model->vehicles as $vehicle) {
       			$entity->addVehicle(new Vehicle($vehicle->vehicle_id,$vehicle->number,$vehicle->model));
       		}
       	}

       	if(!empty($model->photos) && is_array($model->photos)){
       		foreach ($model->photos as $photo) {
       			$entity->addPhoto(new Attachment($photo->out_id, $photo->file_url));
       		}
       	}

        return $entity;
    }


    /**
     * 把对象持久化到数据
     * @param $entity
     * @return BaseEntity
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

        $values = $entity->getAttributes(['name','customer_id','password','phone','remark']);
        $model->setAttributes($values);
        $model->logistics_id = $entity->company->id;
        $model->remark = "添加/修改司机信息";
        $statusEntity  = $entity->getStatus();
        if (!empty($statusEntity))
        {
			$model->status      = $statusEntity->status;
			$model->status_time = $statusEntity->status_time;
        }

        $isNew = $model->isNewRecord;

        $attachments = $entity->photos;
        if(empty($attachments)){
            $attachments = array();
        }
        
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
                $photo->type      = \PhotoAttachment::DRIVER_PHOTO_TYPE;
                $photo->status    = \PhotoAttachment::EFFECTIVE_STATUS;
                $photo->file_url  = $attachment->url;
                $photo->file_path = $attachment->url;

				$res = $photo->save();
                if (!$res)
                    throw new ZModelSaveFalseException($photo);
            }
        }

        // $this->clearCache($entity->customer_id, $this->cacheActive);
        
        $this->clearCache($entity->customer_id, $this->cacheAll);

        return $entity;
    }



    /**
     * @desc 更新状态
     * @param   Driver $entity
     * @throws  \Exception
     */
    public function updateStatus(Driver $entity)
    {
        if (empty($entity))
        {
            throw new ZException("Driver对象不存在");
        }
        $this->activeRecordClassName = $this->getActiveRecordClassName();
		$modelObj = new $this->activeRecordClassName;
		$model    = $modelObj::model()->findByPk($entity->getId());
        if (empty($model))
        {
            throw new ZModelNotExistsException($entity->customer_id, \Utility::getClassBaseName($entity));
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
                throw new ZModelSaveFalseException($model);

            // $this->clearCache($entity->customer_id, $this->cacheActive);
        	
        	$this->clearCache($entity->customer_id, $this->cacheAll);
        }
    }

    /**
     * [bindVehicle 绑定车辆]
     * @param
     * @param  [int] $customerId [客户id]
     * @param  [array(vehicle_id)] $vehicles   [车辆信息]
     * @return [boolean]
     */
    public function bindVehicle($customerId, $vehicles)
    {
        if(empty($customerId))
            throw new ZException("当前客户不存在");

        $entity = $this->findById($customerId);
        if(empty($entity))
            throw new ZException("Driver对象不存在");

        
        $data = \DriverVehicleRelation::model()->findAllToArray("driver_id=".$entity->getId());
        $p    = array();
        if (\Utility::isNotEmpty($data)) {
            foreach ($data as $v) {
                $p[$v["vehicle_id"]] = $v['id'];
            }
        }

        if(\Utility::isNotEmpty($vehicles)){
            foreach ($vehicles as $row) {
                if (array_key_exists($row["vehicle_id"], $p)) {
                    $model = \DriverVehicleRelation::model()->findByPk($p[$row['vehicle_id']]);
                    if (empty($model->id)) {
                        unset($p[$row["vehicle_id"]]);
                        return;
                    }
                } else {
                    $model = new \DriverVehicleRelation();
                    $model->create_time = Utility::getNow();
                }
                $model->driver_id  = $entity->getId();
                $model->vehicle_id = $row["vehicle_id"];
                $res = $model->save();
                if (!$res)
                    throw new ZModelSaveFalseException($res);
                unset($p[$row["vehicle_id"]]);
            }
        }

        if (count($p) > 0) {
            \DriverVehicleRelation::model()->deleteAll('id in(' . implode(',', $p) . ')');
        }

        // $this->clearCache($entity->customer_id, $this->cacheActive);
        
        $this->clearCache($entity->customer_id, $this->cacheAll);

        return true;
    }


    /**
     * [clearCache 清除缓存]
     */
    public function clearCache()
    {
        \Utility::clearCache($this->getCacheKey($this->cacheAll));
    }
}