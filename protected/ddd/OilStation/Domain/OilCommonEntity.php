<?php

namespace ddd\OilStation\Domain;

use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;

abstract class OilCommonEntity extends BaseEntity{

    /**
     * 备注
     * @var   string
     */
    protected $remark;

    /**
     * 生效时间
     * @var   Datetime
     */
    protected $effect_time;

    /**
     * 创建时间
     * @var   Datetime
     */
    protected $create_time;

    /**
     * 更新用户
     * @var   Operator
     */
    protected $update_user;

    /**
     * 更新时间
     * @var   Datetime
     */
    protected $update_time;

    /**
     * 创建用户
     * @var   Operator
     */
    protected $create_user;

    /**
     * 状态
     * @var   Status
     */
    protected $status;

    #region get set methods

    /**
     * @return string
     */
    public function getRemark():string{
        return $this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark(string $remark):void{
        $this->remark = $remark;
    }

    /**
     * @return Datetime
     */
    public function getEffectTime():Datetime{
        return $this->effect_time;
    }

    /**
     * @param Datetime $effect_time
     */
    public function setEffectTime(Datetime $effect_time = null):void{
        if(empty($effect_time)){
            $effect_time = new DateTime();
        }
        $this->effect_time = $effect_time;
    }

    /**
     * @return Datetime
     */
    public function getCreateTime():Datetime{
        return $this->create_time;
    }

    /**
     * @param Datetime $create_time
     */
    public function setCreateTime(Datetime $create_time):void{
        $this->create_time = $create_time;
    }

    /**
     * @return Operator
     */
    public function getUpdateUser():Operator{
        return $this->update_user;
    }

    /**
     * @param Operator $update_user
     */
    public function setUpdateUser(Operator $update_user):void{
        $this->update_user = $update_user;
    }

    /**
     * @return Datetime
     */
    public function getUpdateTime():Datetime{
        return $this->update_time;
    }

    /**
     * @param Datetime $update_time
     */
    public function setUpdateTime(Datetime $update_time):void{
        $this->update_time = $update_time;
    }

    /**
     * @return Operator
     */
    public function getCreateUser():Operator{
        return $this->create_user;
    }

    /**
     * @param Operator $create_user
     */
    public function setCreateUser(Operator $create_user):void{
        $this->create_user = $create_user;
    }

    /**
     * @return Status
     */
    public function getStatus():Status{
        return $this->status;
    }

    /**
     * @param Status $status
     */
    public function setStatus(Status $status):void{
        $this->status = $status;
    }

    #endregion

    #region get ext methods

    public function getCreateUserId():int{
        return $this->create_user->id;
    }

    public function getCreateUserName():string{
        return $this->create_user->name;
    }

    public function getUpdateUserId():int{
        return $this->update_user->id;
    }

    public function getUpdateUserName():string{
        return $this->update_user->name;
    }

    public function getStatusValue():int{
        return $this->status->status;
    }

    public function getStatusName():string{
        return $this->status->name;
    }

    public function getStatusTime():string{
        return $this->status->status_time;
    }

    #endregion
}