<?php
/**
 * This is Entity Repository Interface for ModuleActionTree.
 * Auto Generated.
 * DateTime: 2018-08-29 10:01:48
 * Describe：
 *
 */

namespace app\ddd\Admin\Domain\Module;


/**
 * Interface IModuleActionTreeRepository
 *
 */
interface IModuleActionTreeRepository
{
    /**
     * 加载模块
     * @return ModuleActionTree
     */
    public function load();

    /**
     * 加载有效模块
     * @return ModuleActionTree
     */
    public function loadActive();
}