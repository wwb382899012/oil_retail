<?php

/**
 * @Name            司机绑定的车辆
 * @DateTime        2018年9月5日 14:58:51
 * @Author          Administrator
 */
namespace ddd\Logistics\Domain\Driver;

use ddd\Common\Domain\BaseEntity;

class DriverBindVehicleItem extends BaseEntity
{
    #region property
    
    /**
     * 车辆id 
     * @var   int
     */
    public $vehicle_id;
    
    /**
     * 车牌号 
     * @var   string
     */
    public $number;    

    #endregion
    
    /**
     * 创建工厂方法
     */
    public static function create()
    {
       return new static();
    }
}

