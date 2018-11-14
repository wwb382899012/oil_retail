<?php

namespace ddd\OilStation\DTO\OilPrice;


use ddd\Common\Application\BaseDTO;

class OilPriceDTO extends BaseDTO{
    #region property

    /**
     * 标识
     * @var   int
     */
    public $price_id = 0;

    /**
     * 申请id
     * @var   int
     */
    public $apply_id = 0;

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
     * 油品
     * @var   int
     */
    public $goods_id = 0;

    /**
     * 零售价
     * @var   int
     */
    public $retail_price = 0;

    /**
     * 协议价
     * @var   int
     */
    public $agreed_price = 0;

    /**
     * 优惠价
     * @var   int
     */
    public $discount_price = 0;

    /**
     * 生效时间
     * @var   Datetime
     */
    public $effect_time;

    /**
     * 失效时间
     * @var   Datetime
     */
    public $end_time;

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
     * @return   OilPriceDTO
     */
    public static function create(){
        // TODO: implement
    }

    /**
     * 是否可用油价
     * @return   boolean
     */
    public function isActive(){
        // TODO: implement
    }

    /**
     * 油价校验
     */
    public function checkOilPrice(){
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

    /**
     * 是否可生效
     * @return   boolean
     */
    public function isCanEffect(){
        // TODO: implement
    }

    /**
     * 设为生效
     */
    public function setEffect(){
        // TODO: implement
    }

    /**
     * 设为失效
     */
    public function setTrash(){
        // TODO: implement
    }
}
