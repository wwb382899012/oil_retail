<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 15:32
 * Describe：
 */

namespace app\ddd\Quota\Application\LogisticsQuotaLimit;




use ddd\Common\Application\BaseService;
use app\ddd\Quota\DTO\LogisticsQuotaLimit\LogisticsQuotaLimitDTO;

use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\ZException;

use ddd\Quota\Domain\LogisticsQuotaLimit\LogisticsQuotaLimitRepository;
use ddd\Common\Domain\BaseEntity;

class LogisticsQuotaLimitService extends BaseService
{
    use LogisticsQuotaLimitRepository;
    /**
     * 获取企业限额
     * @param $id
     * @return int
     */
    public function getLogisticsQuotaLimit($id=0,$isArray=true)
    {
        if(empty($id))
            throw new ZException(\RetailError::$PARAMS_PASS_ERROR);

        try
        {
            $entity = $this->getLogisticsQuotaLimitRepository()->findById($id);

            if($entity) {
                $LogisticsQuotaLimitDTO = new LogisticsQuotaLimitDTO();
                $LogisticsQuotaLimitDTO->fromEntity($entity);
                if($isArray)
                    return $LogisticsQuotaLimitDTO->getAttributes();
                else
                    return $LogisticsQuotaLimitDTO;
            }
            else
                return null;//返回异常
        }
        catch (\Exception $e)
        {
            throw new ZException($e->getMessage(),$e->getCode());
        }
    }

    /**
     * 创建对象
     * @name create
     * @param * @param $rate
     * @throw * @throws ZException
     * @return \ddd\Quota\Domain\LogisticsQuotaLimit\LogisticsQuotaLimit|null
     */
    public function create($rate){

        try
        {
            $vehicle = \ddd\Quota\Domain\LogisticsQuotaLimit\LogisticsQuotaLimitService::service()->createLogisticsQuotaLimit($rate);

            if($vehicle) {
                return $vehicle;
            }
            else
                return null;//返回异常
        }
        catch (\Exception $e)
        {
            throw new ZException($e->getMessage(),$e->getCode());
        }

    }

    /**
     * 保存
     * @name save
     * @param * @param BaseEntity $entity
     * @throw * @throws ZException
     * @return null
     */
    public function save(BaseEntity $entity){
        if(empty($entity))
            throw new ZException("LogisticsQuotaLimit对象不存在");

        try
        {
            $re = $this->getLogisticsQuotaLimitRepository()->store($entity);

            if($re) {
                return $re;
            }
            else
                return null;//返回异常
        }
        catch (\Exception $e)
        {
            throw new ZException($e->getMessage(),$e->getCode());
        }

    }

    /**
     * 获取可用当日限额设置
     * @return \ddd\Quota\Domain\LogisticsQuotaLimit\LogisticsQuotaLimit
     * @throws \Exception
     */
    public function getActiveLogisticsQuotaLimit()
    {
        return DIService::get(\ddd\Quota\Domain\LogisticsQuotaLimit\LogisticsQuotaLimitService::class)->getActiveLogisticsQuotaLimit();
    }
}