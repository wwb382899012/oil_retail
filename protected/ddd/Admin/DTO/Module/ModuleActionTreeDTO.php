<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 10:14
 * Describe：
 */

namespace app\ddd\Admin\DTO\Module;


use app\ddd\Admin\Domain\Module\ModuleActionTree;
use ddd\Common\Application\BaseDTO;

class ModuleActionTreeDTO extends BaseDTO
{
    /**
     * 标识
     * @var   int
     */
    public $id = 0;

    /**
     * 模块名称
     * @var   string
     */
    public $label;

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
     * @var array
     */
    public $actions = [];

    public $status;

    /**
     * 子项
     * @var array
     */
    protected $children = [];

    public function customAttributeNames()
    {
        return ["children"];
    }

    /**
     * 添加子菜单
     * @param ModuleActionTreeDTO $child
     */
    public function addChild(ModuleActionTreeDTO $child)
    {
        $this->children[]= $child;
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
    public function getChildren() {
        return array_values($this->children);
    }

    public function fromEntity(ModuleActionTree $tree) {
        $this->setAttributes($tree->getAttributes());
        $this->label = $tree->name;
        $this->status=$tree->getStatus()->status;
        $this->actions = $tree->getActions();
        $children = $tree->getChildren();

        if (\CheckUtility::isNotEmpty($children)) {
            $this->clearChildren();
            foreach ($children as $child) {
                $childMenuDTO = new ModuleActionTreeDTO();
                $childMenuDTO->fromEntity($child);
                $this->addChild($childMenuDTO);
            }
        }
    }

    /**
     * 根据菜单实体创建DTO
     * @param ModuleActionTree $tree
     * @return static
     */
    public static function createFromTree(ModuleActionTree $tree) {
        $dto = new static();
        $dto->fromEntity($tree);
        return $dto;
    }

    
}