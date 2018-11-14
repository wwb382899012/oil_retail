<?php
/**
 * Desc:
 * User: vector
 * Date: 2018/9/6
 * Time: 17:29
 */

namespace ddd\Logistics\Repository;

use app\ddd\Admin\Application\User\UserService;
use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use app\ddd\Common\Repository\RedisHashCache;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;

use ddd\Common\Repository\EntityRepository;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\Utility;
use ddd\Infrastructure\error\ZException;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;
use ddd\Logistics\Domain\LogisticsCompany\ILogisticsCompanyRepository;
use ddd\Logistics\Domain\LogisticsCompany\LogisticsCompany;
use ddd\Logistics\Domain\LogisticsCompany\LogisticsCompanyCreditQuota;

class LogisticsCompanyRepository extends EntityRepository implements ILogisticsCompanyRepository
{
	use RedisHashCache;

    public  $cacheAll    = '_all';
    public  $cacheActive = '_active';

    public function init()
    {
        $this->with=["logisticsCreditQuota"];
    }

	public function getNewEntity()
    {
        return new LogisticsCompany();
    }

    public function getActiveRecordClassName()
    {
        return 'LogisticsCompany';
    }

    public function findById($logisticsId)
    {
        $entity = "";
        if(empty($logisticsId))
            return $entity;
        

    	$entity = $this->getEntityFromCache($logisticsId, $this->cacheAll);

    	if(!empty($entity))
    		return $entity;

    	$entity = parent::findById($logisticsId);

        if (!empty($entity))
        {
        	$this->setCache($logisticsId, $entity, $this->cacheAll);
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
        $values = $model->getAttributes(['name','out_identity','remark']);
        $entity->setId($model->logistics_id);
        $entity->setAttributes($values);
        $entity->setStatus(new Status($model->status, $model->status_time));
        $entity->setOutStatus(new Status($model->out_status));
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

        $creditQuota = $model->logisticsCreditQuota;
        if(!empty($creditQuota)){
            $LogisticsCompanyCreditQuota = new LogisticsCompanyCreditQuota();
            $LogisticsCompanyCreditQuota = $LogisticsCompanyCreditQuota::create($creditQuota->credit_quota, $creditQuota->start_date, $creditQuota->end_date);
            $entity->addCreditorQuota($LogisticsCompanyCreditQuota);
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

        $model->name         = $entity->name;
        $model->out_identity = $entity->out_identity;
        $model->remark       = "添加物流企业";
        
        $statusEntity = $entity->getStatus();
        if (!empty($statusEntity))
        {
            $model->status = $statusEntity->status;
            $model->status_time = $statusEntity->status_time;
        }

        $outStatusEntity = $entity->getOutStatus();
        if (!empty($outStatusEntity))
            $model->out_status = $outStatusEntity->status;

        $creditQuotaModel = $model->logisticsCreditQuota;
        $creditQuota      = $entity->credit_quota;

        $res = $model->save();
        if ($res !== true)
	        throw new ZModelSaveFalseException($model);

        $entity->setId($model->getPrimaryKey());

        if(empty($creditQuotaModel)){
            $creditQuotaModel = new \LogisticsCreditQuota();
            $creditQuotaModel->logistics_id = $entity->getId();
        }

        $creditQuotaModel->credit_quota = $creditQuota->credit_quota->amount;
        $creditQuotaModel->start_date   = $creditQuota->start_date->toDate();
        $creditQuotaModel->end_date     = $creditQuota->end_date->toDate();
        $res = $creditQuotaModel->save();
        if ($res !== true)
            throw new ZModelSaveFalseException($creditQuotaModel);

        if($model->status == \LogisticsCompany::EFFECTIVE_STATUS && 
            $model->out_status == \LogisticsCompany::EFFECTIVE_OUT_STATUS){
        	$this->setCache($entity->getId(), $entity, $this->cacheActive);
        }else{
        	
        }

        // $this->clearCache($entity->getId(), $this->cacheActive);

        $this->clearCache($entity->getId(), $this->cacheAll);

        return $entity;
    }


    /**
     * @desc 更新状态
     * @param   LogisticsCompany $entity
     * @throws  \Exception
     */
    public function updateStatus(LogisticsCompany $entity)
    {
        if (empty($entity))
            throw new ZException("LogisticsCompany对象不存在");

        $this->activeRecordClassName = $this->getActiveRecordClassName();
		$modelObj = new $this->activeRecordClassName;
		$model    = $modelObj::model()->findByPk($entity->getId());
        if (empty($model))
            throw new ZModelNotExistsException($entity->getId(), \Utility::getClassBaseName($entity));
        
        $mark = false;
        $outStatus = $entity->getOutStatus();
        if($model->out_status != $outStatus->status){
            $mark = true;
            $model->out_status = $outStatus->status;
        }

        $statusEntity = $entity->getStatus();
        if ($model->status != $statusEntity->status)
        {
            $mark = true;
            $model->status      = $statusEntity->status;
            $model->status_time = $statusEntity->status_time;
        }

        if($mark){
            $model->update_user_id = \Utility::getNowUserId();
            $model->update_time    = Utility::getNow();
            $res = $model->save();
            if ($res !== true)
                throw new ZModelSaveFalseException($model);

            // $this->clearCache($entity->getId(), $this->cacheActive);

            $this->clearCache($entity->getId(), $this->cacheAll);
        }

        return true;
    }


    public function getLogisticsIdByIdentity($outIdentity)
    {
        $logisticsId = 0;
        if(empty($outIdentity))
            throw new ZException("参数有误");

        $keyName     = $this->getCacheKey('_'.$outIdentity);
        $logisticsId = \Utility::getCache($keyName);

        if(!empty($logisticsId))
            return $logisticsId;

        $entity = $this->find("out_identity='".$outIdentity."'");
        if(!empty($entity)){
            $logisticsId = $entity->getId();
            \Utility::setCache($keyName, $logisticsId);
        }

        return $logisticsId;
    }

    /**
     * [clearCache 清除缓存]
     * @param
     * @param  int $id [keyName]
     * @return [bool]
     */
    public function clearCache($id=0)
    {
        \Utility::clearCache($this->getCacheKey($this->cacheAll));

        if(!empty($id))
            \Utility::clearCache($this->getCacheKey('_'.$id));
    }

}