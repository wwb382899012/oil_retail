<?php
/**
 * Desc:
 * User: vector
 * Date: 2018/9/6
 * Time: 17:29
 */

namespace ddd\Customer\Repository;

use app\ddd\Admin\Application\User\UserService;
use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use app\ddd\Common\Repository\RedisHashCache;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\Common\Repository\EntityRepository;
use ddd\Customer\Domain\Customer;
use ddd\Customer\Domain\ICustomerRepository;
use ddd\Customer\Domain\WXRelation;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\Utility;
use ddd\Infrastructure\error\ZException;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;

class CustomerRepository extends EntityRepository implements ICustomerRepository
{
	use RedisHashCache;

    public  $cacheAll    = '_all';
    public  $cacheActive = '_active';

	public function init()
    {
        $this->with=["wxRelation"];
    }

	public function getNewEntity()
    {
        return new Customer();
    }

    public function getActiveRecordClassName()
    {
        return 'Customer';
    }

	public function findById($customerId)
    {

    	$entity = $this->getEntityFromCache($customerId, $this->cacheAll);

    	if(!empty($entity))
    		return $entity;

    	$entity = parent::findById($customerId);
        if (!empty($entity))
        {
        	$this->setCache($customerId, $entity, $this->cacheAll);
        }

        return $entity;

    }

    /**
     * [getCustomerIdByPhone 根据phone获取customer_id]
     * @param
     * @param  [string] $phone [电话号码]
     * @return [int]
     */
    public function getCustomerIdByPhone($phone)
    {
        $customerId = 0;
        if(empty($phone))
            throw new ZException("参数有误");

        $keyName    = $this->getCacheKey('_'.$phone);
        $customerId = \Utility::getCache($keyName);
        if(!empty($customerId))
            return $customerId;

        $entity = $this->find("phone=".$phone);
        if(!empty($entity)){
            $customerId = $entity->getId();
            \Utility::setCache($keyName, $customerId);
        }

        return $customerId;

    }


    /**
     * [getCustomerIdByOpenId 根据openId获取customer_id]
     * @param
     * @param  [string] $phone [微信标识id]
     * @return [int]
     */
    public function getCustomerIdByOpenId($openId)
    {
        $customerId = 0;
        if(empty($openId))
            throw new ZException("参数有误");

        $keyName    = $this->getCacheKey('_'.$openId);
        $customerId = \Utility::getCache($keyName);

        if(!empty($customerId))
            return $customerId;

        $wxRelation = \CustomerWxRelation::model()->find("open_id='".$openId."' and wx_identity=".\CustomerWxRelation::MINI_PROGRAM);
        if(!empty($wxRelation)){
            $customerId = $wxRelation->customer_id;
            \Utility::setCache($keyName, $customerId);
        }
        return $customerId;
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
        $values = $model->getAttributes(['account','phone','login_count','remark']);
        $entity->setId($model->id);
        $entity->setAttributes($values);
        $entity->setStatus(new Status($model->status, $model->status_time));
        $entity->register_time = new DateTime($model->register_time);
        $entity->login_time    = new DateTime($model->login_time);
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
       	if(!empty($model->wxRelations) && is_array($model->wxRelations)){
       		foreach ($model->wxRelations as $wx) {
                $wxRelation = WXRelation::create($wx->open_id, $wx->wx_identity);
       			$entity->addWeixin($wxRelation);
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
        }else{
            $model->login_count   = 1;
            $model->register_time = Utility::getNow();
            $model->phone         = $entity->phone;
            $model->account       = $entity->phone;
        }
        
        $statusEntity = $entity->getStatus();
        $model->status      = $statusEntity->status;
        $model->status_time = $statusEntity->status_time;
        $model->login_time  = Utility::getNow();
        $model->login_count+= 1;

        $res = $model->save();
        if ($res !== true)
	        throw new ZModelSaveFalseException($model);
	    
        $entity->setId($model->getPrimaryKey());

        // $this->clearCache($entity->getId(), $this->cacheActive);
        
        $this->clearCache($entity->getId(), $this->cacheAll);

        return $entity;
    }


    
    /**
     * @desc 更新状态
     * @param   Customer $entity
     * @throws  \Exception
     */
    public function updateStatus(Customer $entity)
    {
        if (empty($entity))
        {
            throw new ZException("Customer对象不存在");
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
                throw new ZModelSaveFalseException($model);


        	// $this->clearCache($entity->getId(), $this->cacheActive);
        	
        	$this->setCache($entity->getId(), $this->cacheAll);
        }
    }


    /**
     * [bindWeixin 绑定微信]
     * @param  $customerId [客户id]
     * @param  $openId [微信标识id]
     * @return [boolean]
     */
    public function bindWeixin($customerId, $openId)
    {
        if(empty($customerId) || empty($openId))
            throw new ZException("参数有误");

        $model = \CustomerWxRelation::model()->find("customer_id=".$customerId." and wx_identity=".\CustomerWxRelation::MINI_PROGRAM);
        if(!empty($model))
            return true;

        $model = new \CustomerWxRelation();
        $model->customer_id = $customerId;
        $model->wx_identity = \CustomerWxRelation::MINI_PROGRAM;
        $model->open_id     = $openId;

        $res = $model->save();
        if ($res !== true)
            throw new ZModelSaveFalseException($model);

        // $this->clearCache($customerId, $this->cacheActive);

        $this->clearCache($customerId, $this->cacheAll);

        return true;
    }


    /**
     * [clearCache 清除缓存]
     */
    public function clearCache($phone="", $openId="")
    {
        \Utility::clearCache($this->getCacheKey($this->cacheAll));

        if(!empty($phone))
            \Utility::clearCache($this->getCacheKey('_'.$phone));

        if(!empty($openId))
            \Utility::clearCache($this->getCacheKey('_'.$openId));
    }
}