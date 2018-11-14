<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 10:13
 * Describe：
 */

namespace app\ddd\Admin\DTO\Menu;


use app\ddd\Admin\Domain\Menu\Menu;
use ddd\Common\Application\BaseDTO;

class MenuDTO extends BaseDTO
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
     * @var array
     */
    protected $children=[];

    #endregion

    public function customAttributeNames()
    {
        return ["children"];
    }


    /**
     * 添加子菜单
     * @param MenuDTO $childMenu
     */
    public function addChild(MenuDTO $childMenu)
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

    public function fromEntity(Menu $menu)
    {
        $menuAttributes=$menu->getAttributes();
        unset($menuAttributes['children']);
        $this->setAttributes($menuAttributes);
        $children=$menu->getChildren();
        if(is_array($children))
        {
            foreach ($children as $child)
            {
                $childMenuDTO=new MenuDTO();
                $childMenuDTO->fromEntity($child);
                $this->addChild($childMenuDTO);
            }
        }
    }

    /**
     * 根据菜单实体创建DTO
     * @param Menu $menu
     * @return MenuDTO
     */
    public static function createFromMenu(Menu $menu)
    {
        $dto=new static();
        $dto->fromEntity($menu);
        return $dto;
    }

}