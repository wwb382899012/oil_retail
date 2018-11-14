<?php

/**
 * @Name            司机
 * @DateTime        2018年9月5日 15:34:44
 * @Author          Administrator
 */

namespace ddd\Logistics\Domain\Driver;

use app\ddd\Common\Domain\Value\Attachment;
use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use app\ddd\Common\Domain\Value\Vehicle;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\Common\IAggregateRoot;
use ddd\Infrastructure\error\ZException;
use ddd\Logistics\Domain\LogisticsCompany\LogisticsCompany;

class Driver extends BaseEntity implements IAggregateRoot
{
    #region property
    
    /**
     * 标识id 
     * @var   int
     */
    public $id;
    
    /**
     * 用户标识 
     * @var   int
     */
    public $customer_id;
    
    /**
     * 姓名 
     * @var   string
     */
    public $name;

    /**
     * 交易密码 
     * @var   string
     */
    public $password;
    
    /**
     * 所属企业 
     * @var   LogisticsCompany
     */
    public $company;
    
    /**
     * 手机号 
     * @var   string
     */
    public $phone;
    
    /**
     * 车辆信息 
     * @var   Vehicle
     */
    public $vehicle_items;

    /**
     * 驾驶证照片 
     * @var   Attachment
     */
    public $photos;    
    
    /**
     * 生效时间 
     * @var   DateTime
     */
    public $effect_time;
    
    /**
     * 状态 
     * @var   Status
     */
    protected $status;

    /**
     * 状态时间
     * @var   DateTime
     */
    public $status_time;
    
    /**
     * 创建时间 
     * @var   DateTime
     */
    public $create_time;
    
    /**
     * 更新用户 
     * @var   Operator
     */
    public $update_user;
    
    /**
     * 更新时间 
     * @var   DateTime
     */
    public $update_time;
    
    /**
     * 创建用户 
     * @var   Operator
     */
    public $create_user;    

    #endregion
    
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id=$id;
    }

    /**
     * 创建工厂方法
     */
    public static function create()
    {
       return new static();
    }
    
    /**
     * 设置状态
     * @param    Status $status
     */
    public function setStatus(Status $status)
    {
       $this->status = $status;
    }
    
    /**
     * 获取状态
     * @return   Status
     */
    public function getStatus()
    {
       return $this->status;
    }
    
    /**
     * 是否可用
     * @return   boolean
     */
    public function isActive()
    {
       $status = $this->getStatus();
       return $status->status == \Driver::EFFECTIVE_STATUS;
    }
    
    /**
     * 司机绑定车辆
     */
    public function addVehicle(Vehicle $vehicle)
    {
       $this->vehicle_items[$vehicle->id]=$vehicle;
    }
    
    /**
     * 司机解除绑定车辆
     * @return   boolean
     */
    public function removeVehicle($vehicleId)
    {
       unset($this->vehicle_items[$vehicleId]);
       return true;
    }


    /**
     * 添加照片附件
     */
    public function addPhoto(Attachment $photo)
    {
        if (empty($photo))
        {
            throw new ZException("Attachment对象不存在");
        }

        $this->photos[$photo->id]=$photo;
    }


    /**
     * 移除照片附件
     * @return   boolean
     */
    public function removePhoto($id)
    {
       unset($this->photos[$id]);
       return true;
    }
    
}
