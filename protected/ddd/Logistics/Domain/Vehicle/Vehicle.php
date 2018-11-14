<?php

/**
 * @Name            车辆
 * @DateTime        2018年9月5日 16:41:16
 * @Author          Susie
 */

namespace ddd\Logistics\Domain\Vehicle;

use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\Common\IAggregateRoot;
use ddd\Logistics\Domain\LogisticsCompany\LogisticsCompany;

class Vehicle extends BaseEntity implements IAggregateRoot
{
    #region property
    
    /**
     * 标识 
     * @var   int
     */
    public $vehicle_id = 0;
    
    /**
     * 物流企业 
     * @var   LogisticsCompany
     */
    public $company;
    
    /**
     * 车牌号 
     * @var   string
     */
    public $number;
    
    /**
     * 车型 
     * @var   string
     */
    public $model;
    
    /**
     * 油箱容量 
     * @var   float
     */
    public $capacity = 0;
    
    /**
     * 添加人 
     * @var   string
     */
    public $optor;
    
    /**
     * 行驶证 
     * @var   DrivingLicense
     */
    public $driving_license;
    
    /**
     * 备注 
     * @var   string
     */
    public $remark;
    
    /**
     * 状态 
     * @var   Status
     */
    protected $status;

    /**
     * 状态时间 
     * @var   Datetime
     */
    protected $status_time;
    
    /**
     * 生效时间 
     * @var   Datetime
     */
    public $effect_time;
    
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
     * @var   Datetime
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
        return $this->vehicle_id;
    }

    public function setId($vehicleId)
    {
        $this->vehicle_id=$vehicleId;
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
     * 是否可用车辆
     * @return   boolean
     */
    public function isActive()
    {
       $status = $this->getStatus();
       return $status->status == \Vehicle::PASS_STATUS;
    }

    /**
     * 创建
     * @return   Vehicle
     */
    public static function create()
    {
       return new static();
    }


    /**
     * 添加行驶证信息
     */
    public function addDrivingLicense(DrivingLicense $drivingLicense)
    {
        if (empty($drivingLicense))
        {
            throw new ZException("DrivingLicense对象不存在");
        }

        $this->driving_license=$drivingLicense;

        return true;
    }
    
    
    /**
     * 驳回
     * @param    boolean $persistent
     */
    public function reject($persistent)
    {
       // TODO: implement
    }
    
    /**
     * 通过
     * @param    boolean $persistent
     */
    public function checkPass($persistent)
    {
       // TODO: implement
    }
}
