<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 15:40
 * Describe：
 */

namespace app\ddd\Admin\Domain\Menu;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;

class UserMenu extends BaseEntity implements IAggregateRoot
{
    public $user_id;

    /**
     * @var Menu
     */
    public $menu;

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


}