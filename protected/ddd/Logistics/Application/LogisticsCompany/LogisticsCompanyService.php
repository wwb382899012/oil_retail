<?php
/**
 * Created by youyi000.
 * DateTime: 2018/9/3 15:06
 * Describe：物流企业
 */

namespace app\ddd\Logistics\Application\LogisticsCompany;

use app\ddd\Common\Domain\Value\Status;
use app\ddd\Logistics\DTO\LogisticsCompany\LogisticsCompanyDTO;
use ddd\Common\Application\BaseService;
use ddd\Common\Application\Transaction;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\ZException;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\Utility;
use ddd\Logistics\Domain\LogisticsCompany\ILogisticsCompanyRepository;
use ddd\Logistics\Domain\LogisticsCompany\LogisticsCompany;

class LogisticsCompanyService extends BaseService
{
    use Transaction;

    /**
     * 获取物流企业
     * @param $id
     * @return int
     */
    public function getLogisticsCompany($id=0,$isArray=true)
    {
        if(empty($id))
            throw new ZException("参数有误");

        try
        {
            $entity = DIService::getRepository(ILogisticsCompanyRepository::class)->findById($id);

            if(empty($entity))
                throw new ZException("当前物流企业信息不存在");

            $LogisticsCompanyeDTO = new LogisticsCompanyDTO();
            $LogisticsCompanyeDTO->fromEntity($entity);

            if($isArray)
                return $LogisticsCompanyeDTO->getAttributes();
            else
                return $LogisticsCompanyeDTO;
        }
        catch (\Exception $e)
        {
            throw new ZException($e->getMessage(),$e->getCode());
        }
    }

    /**
     * 获取物流企业实体
     * @param int $id
     * @return LogisticsCompany
     * @throws ZException
     * @throws ZModelNotExistsException
     */
    public function getEntityById(int $id):LogisticsCompany{
        if(empty($id)){
            throw new ZException("参数有误");
        }

        $entity = DIService::getRepository(ILogisticsCompanyRepository::class)->findById($id);
        if(empty($entity)){
            throw new ZModelNotExistsException($id, LogisticsCompany::class);
        }

        return $entity;
    }

    /**
     * 添加物流企业
     * @name addLogisticsCompany
     * @param * @param LogisticsCompany $entity
     * @throw * @throws ZException
     * @return LogisticsCompany
     */
    public function addLogisticsCompany(LogisticsCompany $entity){
        if(empty($entity))
            throw new ZException("当前物流企业信息不存在");

        if(!$this->isCanAdd($entity->out_identity))
            throw new ZException("当前标识的物流企业已经存在");

        try
        {
            $this->beginTransaction();

            $entity = DIService::getRepository(ILogisticsCompanyRepository::class)->store($entity);

            $this->commitTransaction();

            \ddd\Quota\Application\LogisticsQuota\LogisticsQuotaService::service()->createLogisticsQuota($entity->getId());

            //推送物流企业信息给财务系统
            \AMQPService::publishLogisticsCompanyToFinanceSystem($entity->getId());

            return $entity;
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw new ZException($e->getMessage(),$e->getCode());
        }

    }

    public function isCanAdd($identity)
    {
        if(empty($identity))
            throw new ZException("参数有误");

        $logisticsId = DIService::getRepository(ILogisticsCompanyRepository::class)->getLogisticsIdByIdentity($identity);
        if(!empty($logisticsId))
            return false;

        return true;
    }

    /**
     * 保存
     * @name save
     * @param * @param $entity
     * @throw * @throws ZException
     * @return mixed
     */
    public function save($entity){
        if(empty($entity))
            throw new ZException("参数有误");

        try
        {
            $this->beginTransaction();

            $entity = DIService::getRepository(ILogisticsCompanyRepository::class)->store($entity);

            $this->commitTransaction();

            //推送物流企业信息给财务系统
            \AMQPService::publishLogisticsCompanyToFinanceSystem($entity->getId());

            return $entity;
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw new ZException($e->getMessage(),$e->getCode());
        }
    }


    /**
     * [updateStatus 更新银管家物流企业状态]
     * @param
     * @param  [int] $identity [银管家物流企业标识]
     * @param  [int] $status   [银管家物流企业状态]
     * @return [bool]
     */
    public function updateOutStatus($identity, $status)
    {
        if(empty($identity) || !isset($status))
            throw new ZException("参数有误");

        $logisticsId = DIService::getRepository(ILogisticsCompanyRepository::class)->getLogisticsIdByIdentity($identity);
        if(empty($logisticsId))
            throw new ZException("identity为".$identity."的物流企业不存在");

        $entity = DIService::getRepository(ILogisticsCompanyRepository::class)->findById($logisticsId);
        if(empty($entity))
            throw new ZException("当前物流企业信息不存在");

        $nowTime = Utility::getNow();

        $entity->setOutStatus(new Status($status,$nowTime));

        try
        {
            $this->beginTransaction();
            
            DIService::getRepository(ILogisticsCompanyRepository::class)->updateStatus($entity);
            
            $this->commitTransaction();

            //推送物流企业信息给财务系统
            \AMQPService::publishLogisticsCompanyToFinanceSystem($entity->getId());

            return true;
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw new ZException($e->getMessage(),$e->getCode());
        }
    }


    /**
     * [updateStatus 更新物流企业状态]
     * @param
     * @param  [int] $logisticsId [物流企业标识]
     * @param  [int] $status   [物流企业状态]
     * @return [bool]
     */
    public function updateStatus($logisticsId, $status)
    {
        if(empty($logisticsId) || !isset($status))
            throw new ZException("参数有误");

        $entity = DIService::getRepository(ILogisticsCompanyRepository::class)->findById($logisticsId);
        if(empty($entity))
            throw new ZException("当前物流企业信息不存在");

        $nowTime = Utility::getNow();

        $entity->setStatus(new Status($status,$nowTime));

        try
        {
            $this->beginTransaction();
            
            DIService::getRepository(ILogisticsCompanyRepository::class)->updateStatus($entity);
            
            $this->commitTransaction();

            //推送物流企业信息给财务系统
            \AMQPService::publishLogisticsCompanyToFinanceSystem($entity->getId());

            return true;
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw new ZException($e->getMessage(),$e->getCode());
        }
    }


}