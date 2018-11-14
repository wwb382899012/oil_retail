<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/24 14:42
 * Describe：
 */

namespace app\ddd\Admin\Domain\Right;



use app\ddd\Common\Domain\Value\Operator;

use ddd\Common\IAggregateRoot;

class RoleRight extends BaseRight implements IAggregateRoot
{
    #region property

    /**
     * 标识
     * @var   int
     */
    public $role_id;

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
     * @var   /Datetime
     */
    public $update_time;

    /**
     * 更新用户
     * @var   Operator
     */
    public $update_user;

    #endregion

    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->role_id;
    }

    public function setId($value)
    {
        // TODO: Implement setId() method.
        $this->role_id=$value;
    }


}