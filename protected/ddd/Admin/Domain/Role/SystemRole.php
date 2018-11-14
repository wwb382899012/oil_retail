<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/24 11:49
 * Describe：
 */

namespace app\ddd\Admin\Domain\Role;


use app\ddd\Common\Domain\Value\Operator;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;

class SystemRole extends BaseEntity implements IAggregateRoot
{
    #region property

    /**
     * 标识
     * @var   int
     */
    public $role_id = 0;

    /**
     * 名称
     * @var   string
     */
    public $name;

    /**
     * 权限码
     * @var   string
     */
    public $right_codes;

    /**
     * 排序码
     * @var   int
     */
    public $order_index = 0;

    /**
     * 类型
     * @var   int
     */
    public $type = 0;

    /**
     * 状态
     * @var   int
     */
    public $status = 0;

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