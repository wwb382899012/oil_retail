<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/29 9:56
 * Describe：
 */

namespace app\ddd\Admin\Domain\Module;


use ddd\Common\IAggregateRoot;

class ModuleActionTree extends ModuleAction implements IAggregateRoot
{
    /**
     * 父模块id
     * @var   int
     */
    public $parent_id = 0;

    /**
     * 子项
     * @var ModuleActionTree[]
     */
    protected $children=[];

    public function customAttributeNames()
    {
        return ["children"];
    }

    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->id;
    }

    public function setId($value)
    {
        // TODO: Implement setId() method.
        $this->id=$value;
    }


    /**
     * 添加子菜单
     * @param ModuleActionTree $child
     */
    public function addChild(ModuleActionTree $child)
    {
        $this->children[$child->id]=$child;
    }

    /**
     * 移除子菜单
     * @param $moduleId
     */
    public function removeChild($moduleId)
    {
        if(empty($moduleId))
            return;
        if(key_exists($moduleId,$this->children))
            unset($this->children[$moduleId]);
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
}