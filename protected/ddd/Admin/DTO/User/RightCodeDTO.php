<?php
/**
 * User: liyu
 * Date: 2018/9/7
 * Time: 18:47
 * Desc: UserRightDTO.php
 */

namespace ddd\Admin\DTO\User;


use app\ddd\Admin\Domain\Menu\Menu;
use ddd\Common\Application\BaseDTO;
use app\ddd\Admin\DTO\Menu\MenuDTO;

class RightCodeDTO extends BaseDTO
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
     * 父模块id
     * @var   int
     */
    public $parent_id = 0;


    /**
     * 子项
     * @var array
     */
    protected $children = [];

    #endregion

    public function customAttributeNames() {
        return ["children"];
    }


    /**
     * 添加子菜单
     * @param MenuDTO $childMenu
     */
    public function addChild(MenuDTO $childMenu) {
        $this->children[$childMenu->id] = $childMenu;
    }

    /**
     * 移除子菜单
     * @param $menuId
     */
    public function removeChild($menuId) {
        if (empty($menuId))
            return;
        if (key_exists($menuId, $this->children))
            unset($this->children[$menuId]);
    }

    /**
     * 清空子菜单
     */
    public function clearChildren() {
        $this->children = [];
    }

    /**
     * 获取子菜单
     * @return array
     */
    public function getChildren() {
        return array_values($this->children);
    }

    public function fromEntity(Menu $menu) {
        $this->setAttributes($menu->getAttributes());
        $children = $menu->getChildren();
        if (is_array($children)) {
            foreach ($children as $child) {
                $childMenuDTO = new MenuDTO();
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
    public static function createFromMenu(Menu $menu) {
        $dto = new static();
        $dto->fromEntity($menu);
        return $dto;
    }

    public function toEntity(){

    }
}