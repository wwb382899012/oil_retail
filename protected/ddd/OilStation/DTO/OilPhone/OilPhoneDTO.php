<?php

namespace ddd\OilStation\DTO\OilPhone;


use ddd\Common\Application\BaseDTO;

class OilPhoneDTO extends BaseDTO{
    #region property

    /**
     * 标识
     * @var   int
     */
    public $phone_id = 0;

    /**
     * 油企
     * @var   int
     */
    public $company_id = 0;

    /**
     * 油站
     * @var   int
     */
    public $station_id = 0;

    /**
     * 用途
     * @var   EnumValue
     */
    public $use_type;

    /**
     * 电话号码
     * @var   string
     */
    public $phone_number;

    /**
     * 备注
     * @var   string
     */
    public $remark;
    /**
     * 创建时间
     * @var   Datetime
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
    /**
     * 状态
     * @var   Status
     */
    protected $status;

    #endregion

    /**
     * 创建
     * @return   OilPhoneDTO
     */
    public static function create(){
        // TODO: implement
    }

    /**
     * 电话校验
     */
    public function checkOilPhone(){
        // TODO: implement
    }

    /**
     * 获取状态
     * @return   Status
     */
    public function getStatus(){
        // TODO: implement
    }

    /**
     * 设置状态
     * @param    Status $status
     */
    public function setStatus(Status $status){
        // TODO: implement
    }
}