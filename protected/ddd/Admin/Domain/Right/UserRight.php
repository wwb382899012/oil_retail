<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/24 14:41
 * Describe：
 */

namespace app\ddd\Admin\Domain\Right;



use app\ddd\Common\Domain\Value\Operator;

use ddd\Common\IAggregateRoot;

class UserRight extends BaseRight implements IAggregateRoot
{
    #region property

    /**
     * 标识
     * @var   int
     */
    public $user_id;

    /**
     * 是否根据角色自动授权
     * @var   boolean
     */
    public $authorize_with_role;

    /**
     * 创建时间
     * @var   /Datetime
     */
    public $create_time;

    /**
     * 创建用户
     * @var   Operator
     */
    public $create_user;


    /**
     * 更新时间
     * @var   \Datetime
     */
    public $update_time;

    /**
     * 更新用户
     * @var   Operator
     */
    public $update_user;

    #endregion

    public static function create($userId=0){
        $entity=new static();
        $entity->setId($userId);
        return $entity;
    }

    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->user_id;
    }

    public function setId($value)
    {
        // TODO: Implement setId() method.
        $this->user_id=$value;
    }

    public function __sleep()
    {
        // TODO: Implement __sleep() method.
        return ["user_id","authorize_with_role","modules"];
    }

    /*public function __wakeup()
    {
        // TODO: Implement __wakeup() method.
    }*/


}