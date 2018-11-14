<?php
/**
 * This is Entity Repository for ModuleActionTree.
 * Auto Generated.
 * DateTime: 2018-08-29 10:03:15
 * Describe：
 *
 */

namespace app\ddd\Admin\Repository\Module;

use app\ddd\Admin\Domain\Module\ModuleActionTree;
use app\ddd\Admin\Domain\Module\IModuleActionTreeRepository;
use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Repository\BaseRepository;

class ModuleActionTreeRepository extends BaseRepository implements IModuleActionTreeRepository
{

    /**
     * 加载有效模块
     * @return ModuleActionTree|null
     * @throws \Exception
     */
    public function loadActive()
    {
        // TODO: Implement loadActive() method.
        return $this->loadTree(true);
    }

    /**
     * 加载模块
     * @return ModuleActionTree|null
     * @throws \Exception
     */
    public function load()
    {
        // TODO: Implement load() method.
        return $this->loadTree(false);
    }

    /**
     * 加载模块
     * @param bool $isActive
     * @return ModuleActionTree|null
     * @throws \Exception
     */
    protected function loadTree($isActive=true)
    {
        // TODO: Implement load() method.

        $condition=["order"=>"parent_id asc,order_index asc"];
        if($isActive)
            $condition["condition"]="status=1";

        $items=\SystemModule::model()->findAll($condition);
        if(!is_array($items))
            return null;

        $tree=new ModuleActionTree();
        $tree->id=0;

        $this->generateChildren($tree,$items);
        return $tree;
    }

    /**
     * 生成子菜单
     * @param ModuleActionTree $tree
     * @param \SystemModule[] $items
     * @return ModuleActionTree
     * @throws \Exception
     */
    protected function generateChildren(ModuleActionTree &$tree,array &$items)
    {
        foreach ($items as $k=>$item)
        {
            if($item->parent_id==$tree->getId())
            {
                $child=$this->modelToEntity($item);
                $this->generateChildren($child,$items);
                $tree->addChild($child);
                unset($items[$k]);
            }
        }
        return $tree;
    }

    /**
     * 根据model获取实体
     * @param \SystemModule $model
     * @return ModuleActionTree
     * @throws \Exception
     */
    protected function modelToEntity(\SystemModule $model)
    {
        $tree=new ModuleActionTree();
        $tree->setId($model->id);
        $tree->code=$model->code;
        $tree->name=$model->name;
        $tree->parent_id=$model->parent_id;
        $tree->setStatus(new Status($model->status));
        $tree->addActions($model->getActions());

        return $tree;
    }
}
