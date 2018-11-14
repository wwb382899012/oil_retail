<?php
/**
 * Created by youyi000.
 * DateTime: 2018/9/3 15:06
 * Describe：司机
 */

namespace app\ddd\Logistics\Application\Driver;


use app\ddd\Logistics\DTO\Driver\DriverDTO;
use app\ddd\Logistics\DTO\Vehicle\VehicleDTO;
use ddd\Common\Application\BaseService;
use ddd\Common\Application\Transaction;
use ddd\Customer\Domain\Customer;
use ddd\Customer\Domain\ICustomerRepository;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\ZException;
use ddd\Logistics\Domain\Driver\Driver;
use ddd\Logistics\Domain\Driver\IDriverRepository;
use ddd\Logistics\Domain\LogisticsCompany\ILogisticsCompanyRepository;
use ddd\Logistics\Domain\Vehicle\IVehicleRepository;

class DriverService extends BaseService
{
    use Transaction;

    /**
     * 获取司机信息
     * @param $id
     * @return int
     */
    public function getDriver($id=0,$isArray=true)
    {
        if(empty($id))
            throw new ZException("参数有误");

        $driver = DIService::getRepository(IDriverRepository::class)->findById($id);
        if(empty($driver))
            throw new ZException("当前司机信息不存在");

        $dto = new DriverDTO();
        $dto->fromEntity($driver);

        $vehicles = [];
        if(!empty($driver->vehicle_items) && is_array($driver->vehicle_items)){
            foreach ($driver->vehicle_items as $item) {
                $vehicle = DIService::getRepository(IVehicleRepository::class)->findById($item->id);
                if(empty($vehicle))
                    throw new ZException("当前车辆信息不存在");
                if($vehicle->getStatus()->status != \Vehicle::PASS_STATUS)
                    continue;

                $quota = \ddd\Quota\Application\VehicleQuota\VehicleDailyQuotaService::service()->getVehicleDailyQuota($item->id);

                $vehicleDto = new VehicleDTO();
                $vehicleDto->fromEntity($vehicle);
                $vehicleDto->day_capacity = $quota->total_quota;
                $vehicleDto->balance_capacity = $quota->available_quota;
                $vehicles[] = $vehicleDto;
            }
        }

        $dto->vehicles = $vehicles;

        return $dto->getAttributes();
    }


    /**
     * [addDriver 添加司机信息]
     * @param
     * @param Driver $entity [司机实体]
     */
    public function addDriver(Driver $entity)
    {
        if(empty($entity))
            throw new ZException("参数有误");

        if(!$this->isCanAdd($entity->phone))
            throw new ZException("当前手机号的客户信息已经存在");

        $logisticsCompany = DIService::getRepository(ILogisticsCompanyRepository::class)->findById($entity->company->id);
        if(empty($logisticsCompany))
            throw new ZException("当前物流企业不存在");

        try
        {
            $this->beginTransaction();

            $customer = new Customer();
            $customer->phone   = $entity->phone;
            $customer->account = $entity->phone;
            $customer->status  = $entity->getStatus();

            $customer = DIService::getRepository(ICustomerRepository::class)->store($customer);

            $entity->customer_id = $customer->getId();
            $entity = DIService::getRepository(IDriverRepository::class)->store($entity);

            $this->commitTransaction();
            return $entity;
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw new ZException($e->getMessage(),$e->getCode());
        }
    }

    /**
     * [addDriver 修改司机信息]
     * @param
     * @param Driver $entity [司机实体]
     */
    public function editDriver(Driver $entity)
    {
        if(empty($entity))
            throw new ZException("参数有误");

        try
        {
            $this->beginTransaction();

            $entity = DIService::getRepository(IDriverRepository::class)->store($entity);

            $this->commitTransaction();
            return $entity;
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw new ZException($e->getMessage(),$e->getCode());
        }
    }

    /**
     * [validateVehicles 校验传入的vehicles数组中的vehicle_id]
     * @param
     * @param  [type] $vehicles [description]
     * @return [type]
     */
    public static function validateVehicles($vehicles)
    {
        if(!empty($vehicles) && is_array($vehicles)){
            foreach ($vehicles as $vehicle_id) {
                if(empty($vehicle_id))
                    throw new ZException("参数有误");

                $entity = DIService::getRepository(IVehicleRepository::class)->findById($vehicle_id);
                if(empty($entity))
                    throw new ZException("车辆id:".$vehicle_id."车辆信息不存在");
                $status = $entity->getStatus();
                if($status->status != \Vehicle::PASS_STATUS)
                    throw new ZException("车辆id：".$vehicle_id."，车牌号:".$entity->number."的车辆不是有效状态");
            }
        }

        return true;
    }

    /**
     * [bindVehicle 绑定车辆]
     * @param
     * @param  [int] $customerId [客户id]
     * @param  [array(vehicle_id)] $vehicles   [车辆信息]
     * @return [bool]
     */
    public function bindVehicle($customerId, $vehicles)
    {
        if(empty($customerId))
            throw new ZException("参数有误");

        $driver = DIService::getRepository(IDriverRepository::class)->findById($customerId);
        if(empty($driver))
            throw new ZException("当前客户不存在");

        try
        {
            array_map('\app\ddd\Logistics\Application\Driver\DriverService::validateVehicles', $vehicles);

            $this->beginTransaction();

            DIService::getRepository(IDriverRepository::class)->bindVehicle($customerId, $vehicles);

            $this->commitTransaction();
            
            return true;
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw new ZException($e->getMessage(),$e->getCode());
        }
    }


    public function isCanAdd($phone)
    {
        if(empty($phone))
            throw new ZException("参数有误");

        $customerId = DIService::getRepository(ICustomerRepository::class)->getCustomerIdByPhone($phone);
        if(!empty($customerId))
            return false;

        return true;
    }



}