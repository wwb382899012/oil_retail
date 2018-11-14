<?php
/**
 * Created by youyi000.
 * DateTime: 2018/9/3 15:06
 * Describe：车辆
 */

namespace app\ddd\Logistics\Application\Vehicle;

use app\ddd\Logistics\DTO\Vehicle\VehicleDTO;
use ddd\Common\Application\BaseService;
use ddd\Common\Application\Transaction;

use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\ZException;
use ddd\Logistics\Domain\LogisticsCompany\ILogisticsCompanyRepository;
use ddd\Logistics\Domain\Vehicle\IVehicleRepository;
use ddd\Logistics\Domain\Vehicle\Vehicle;

class VehicleService extends BaseService
{
    use Transaction;

    /**
     * @param int $id
     * @param bool $isArray
     * @return VehicleDTO|array
     * @throws \Exception
     */
    public function getVehicle($id=0,$isArray=true)
    {
        if(empty($id))
            throw new ZException("参数有误");

        try
        {
            $entity = DIService::getRepository(IVehicleRepository::class)->findById($id);
            if(empty($entity))
                throw new ZException("当前车辆信息不存在");

            $VehicleDTO = new VehicleDTO();
            $VehicleDTO->fromEntity($entity);

            if($isArray)
                return $VehicleDTO->getAttributes();
            else
                return $VehicleDTO;
        }
        catch (\Exception $e)
        {
            throw new ZException($e->getMessage(),$e->getCode());
        }
    }

    /**
     * [addVehicle 添加车辆]
     * @param Vehicle $entity [车辆实体]
     * @return Vehicle
     * @throws \Exception
     */
    public function addVehicle(Vehicle $entity){
        if(empty($entity))
            throw new ZException("当前车辆信息不存在");

        $logisticsCompany = DIService::getRepository(ILogisticsCompanyRepository::class)->findById($entity->company->id);
        if(empty($logisticsCompany))
            throw new ZException("当前物流企业不存在");

        if(!$this->isCanAdd($entity->number))
            throw new ZException("当前车牌号车辆已经存在");

        try
        {
            $this->beginTransaction();

            $entity = DIService::getRepository(IVehicleRepository::class)->store($entity);

            $this->commitTransaction();
            return $entity;
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw new ZException($e->getMessage(),$e->getCode());
        }

    }


    public function isCanAdd($number)
    {
        if(empty($number))
            throw new ZException("参数有误");

        $vehicleId = DIService::getRepository(IVehicleRepository::class)->getVehicleIdByNumber($number);
        if(!empty($vehicleId))
            return false;

        return true;
    }

}