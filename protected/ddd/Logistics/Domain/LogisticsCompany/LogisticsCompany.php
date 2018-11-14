<?php

/**
 * @Name            物流企业额度
 * @DateTime        2018年9月6日 10:02:47
 * @Author          vector
 */

namespace ddd\Logistics\Domain\LogisticsCompany;

use app\ddd\Common\Domain\Value\Operator;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\Common\IAggregateRoot;
use app\ddd\Common\Domain\Value\Status;
use ddd\Infrastructure\error\ZException;

class LogisticsCompany extends BaseEntity implements IAggregateRoot
{
    #region property
    
    /**
     * 标识 
     * @var   int
     */
    public $logistics_id = 0;
    
    /**
     * 企业名称 
     * @var   string
     */
    public $name;
    
    /**
     * 银管家状态 
     * @var   Status
     */
    public $out_status;
    
    /**
     * 银管家标识 
     * @var   string
     */
    public $out_identity;
    
    /**
     * 纳税人识别号 
     * @var   string
     */
    public $tax_code;
    
    /**
     * 电话 
     * @var   string
     */
    public $phone;
    
    /**
     * 注册地址 
     * @var   string
     */
    public $address;

    /**
     * 授信额度
     * @var   LogisticsCompanyCreditQuota
     */
    public $credit_quota;
    
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
     * @var   DateTime
     */
    public $status_time;
    
    /**
     * 生效时间 
     * @var   DateTime
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
        return $this->logistics_id;
    }

    public function setId($logisticsId)
    {
        $this->logistics_id=$logisticsId;
    }

    /**
     * 创建
     * @return   LogisticsCompany
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
     * 是否可用物流企业
     * @return   boolean
     */
    public function isActive()
    {
       $status    = $this->getStatus();
       $outStatus = $this->getOutStatus();
       return $status->status == \LogisticsCompany::EFFECTIVE_STATUS && $outStatus->status == \LogisticsCompany::EFFECTIVE_OUT_STATUS;
    }
    
    /**
     * 设置银管家状态
     * @param    Status $outStatus
     */
    public function setOutStatus(Status $outStatus)
    {
       $this->out_status = $outStatus;
    }
    
    /**
     * 获取银管家状态
     * @return   Status
     */
    public function getOutStatus()
    {
       return $this->out_status;
    }


    /**
     * 添加行驶证信息
     * @param LogisticsCompanyCreditQuota $creditQuota
     * @return bool
     * @throws \Exception
     */
    public function addCreditorQuota(LogisticsCompanyCreditQuota $creditQuota)
    {
        if (empty($creditQuota))
        {
            throw new ZException("LogisticsCompanyCreditQuota对象不存在");
        }

        $this->credit_quota=$creditQuota;

        return true;
    }
}
