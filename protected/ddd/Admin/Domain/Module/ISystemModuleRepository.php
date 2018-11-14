<?php
/**
 * This is Entity Repository Interface for SystemModule.
 * Auto Generated.
 * DateTime: 2018-08-29 10:52:54
 * Describe：
 *
 */

namespace app\ddd\Admin\Domain\Module;

use ddd\Common\Domain\IRepository;

/**
 * Interface ISystemModuleRepository
 *
 */
interface ISystemModuleRepository extends IRepository
{
    /**
     * @param $id
     * @return SystemModule|null
     */
    function findById($id);

    function store($entity);

    /**
     * 删除模块
     * @param SystemModule $module
     * @return bool
     */
    function delete($module);

    /**
     * @param $parentId
     * @return mixed
     */
    function findModulesByParentId($parentId);

    /**
     * @desc 根据权限码 获取模块
     * @param $code
     * @return mixed
     */
    function findByCode($code);
}