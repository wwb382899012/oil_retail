<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 10:17
 * Describe：
 */

namespace app\ddd\Admin\Domain\Menu;


use app\ddd\Admin\Domain\Module\SystemModule;
use ddd\Common\Domain\BaseEntity;

class Menu extends BaseEntity
{

    #region property

    /**
     * 标识
     * @var   int
     */
    public $id = 0;

    /**
     * 模块名称
     * @var   string
     */
    public $name;

    /**
     * 权限码
     * @var   string
     */
    public $code;

    /**
     * 图标
     * @var   string
     */
    public $icon;

    /**
     * 系统id
     * @var   int
     */
    public $system_id = 0;

    /**
     * 父模块id
     * @var   int
     */
    public $parent_id = 0;

    /**
     * 父模块路径
     * @var   string
     */
    public $parent_ids;

    /**
     * 模块地址
     * @var   string
     */
    public $page_url;

    /**
     * 排序码
     * @var   int
     */
    public $order_index = 0;

    /**
     * 是否分开
     * @var   boolean
     */
    public $is_public;

    /**
     * 是否外部链接
     * @var   boolean
     */
    public $is_external;

    /**
     * 备注
     * @var   string
     */
    public $remark;


    /**
     * 子项
     * @var Menu[]
     */
    protected $children=[];

    #endregion

    public function customAttributeNames()
    {
        return ["children"];
    }

    public function getId(){
        return $this->id;
    }

    /**
     * 添加子菜单
     * @param Menu $childMenu
     */
    public function addChild(Menu $childMenu)
    {
        $this->children[$childMenu->id]=$childMenu;
    }

    /**
     * 移除子菜单
     * @param $menuId
     */
    public function removeChild($menuId)
    {
        if(empty($menuId))
            return;
        if(key_exists($menuId,$this->children))
            unset($this->children[$menuId]);
    }

    /**
     * 清空子菜单
     */
    public function clearChildren()
    {
        $this->children=[];
    }

    /**
     * 获取子菜单
     * @return array
     */
    public function getChildren()
    {
        return array_values($this->children);
    }

    /**
     * 通过系统模块创建菜单
     * @param SystemModule $module
     * @return Menu
     * @throws \Exception
     */
    public static function createBySystemModule(SystemModule $module)
    {
        $menu=new Menu();
        $menu->setAttributes($module->getAttributes());
        return $menu;
    }


}