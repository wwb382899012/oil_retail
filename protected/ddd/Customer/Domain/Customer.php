<?php

/**
 * @Name            客户信息
 * @DateTime        2018年9月3日 17:27:17
 * @Author          youyi000
 */

namespace ddd\Customer\Domain;

use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\Common\IAggregateRoot;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ZException;


class Customer extends BaseEntity implements IAggregateRoot
{
    #region property
    
    /**
     * 用户标识 
     * @var   int
     */
    public $id;
    
    /**
     * 用户帐号 
     * @var   string
     */
    public $account;
    
    /**
     * 手机号 
     * @var   string
     */
    public $phone;
    
    /**
     * 注册时间 
     * @var   DateTime
     */
    public $register_time;
    
    /**
     * 登录次数 
     * @var   int
     */
    public $login_count = 0;
    
    /**
     * 登录时间 
     * @var   Datetime
     */
    public $login_time;
    
    /**
     * 登录标识 
     * @var   string
     */
    public $token;
    
    /**
     * 微信标识关联 
     * @var   WXRelation
     */
    public $wx_relations;
    
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
        return $status->status == \Customer::EFFECTIVE_STATUS;
    }
    
    /**
     * 客户绑定微信号
     */
    public function addWeixin(WXRelation $weixin)
    {
       if (empty($weixin))
            throw new ZException("WXRelation对象不存在");

        $identity = $weixin->wx_identity;
        if ($this->identityIsBound($identity))
            throw new ZException(BusinessError::Customer_Wx_Is_Bound);

       $this->wx_relations[$weixin->wx_identity]=$weixin;
    }
    
    /**
     * 客户解除微信绑定
     * @return   boolean
     */
    public function removeWeixin($identity)
    {
       unset($this->wx_relations[$identity]);
       return true;
    }


    /**
     * 判断当前账户是否已经绑定对应平台标识的微信号
     * @param $identity
     * @return bool
     */
    public function identityIsBound($identity) {
        return isset($this->wx_relations[$identity]);
    }
}
