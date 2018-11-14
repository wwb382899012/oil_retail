<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 15:32
 * Describe：
 */

namespace app\ddd\Quota\Application\VehicleQuotaLimit;




use ddd\Common\Application\BaseService;

use app\ddd\Quota\DTO\VehicleQuotaLimit\VehicleQuotaLimitDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\ZException;

use ddd\Quota\Domain\VehicleQuotaLimit\VehicleQuotaLimitRepository;

class VehicleQuotaLimitService extends BaseService
{
    use VehicleQuotaLimitRepository;
    /**
     * 获取车辆限额
     * @param $id
     * @return int
     */
    public function getVehicleQuotaLimit($id=0,$isArray=true)
    {
        if(empty($id))
            throw new ZException(\RetailError::$PARAMS_PASS_ERROR);

        try
        {
            $entity = $this->getVehicleQuotaLimitRepository()->findById($id);

            if($entity) {
                $VehicleQuotaLimitDTO = new VehicleQuotaLimitDTO();
                $VehicleQuotaLimitDTO->fromEntity($entity);
                if($isArray)
                     return $VehicleQuotaLimitDTO->getAttributes();
                else
                     return $VehicleQuotaLimitDTO;
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
     * @return \ddd\Quota\Domain\VehicleQuotaLimit\VehicleQuotaLimit|null
     */
    public function create($rate){

        try
        {
            $vehicle = \ddd\Quota\Domain\VehicleQuotaLimit\VehicleQuotaLimitService::service()->createVehicleQuotaLimit($rate);

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
            throw new ZException("VehicleQuotaLimit对象不存在");

        try
        {
            $re = $this->getVehicleQuotaLimitRepository()->store($entity);

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
     * @return \ddd\Quota\Domain\VehicleQuotaLimit\VehicleQuotaLimit
     * @throws \Exception
     */
    public function getActiveVehicleQuotaLimit()
    {
        return DIService::get(\ddd\Quota\Domain\VehicleQuotaLimit\VehicleQuotaLimitService::class)->getActiveVehicleQuotaLimit();
    }
}