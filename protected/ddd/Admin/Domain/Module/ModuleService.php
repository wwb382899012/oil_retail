<?php
/**
 * User: liyu
 * Date: 2018/9/13
 * Time: 11:01
 * Desc: ModuleService.php
 */

namespace ddd\Admin\Domain\Module;


use app\ddd\Admin\Domain\Module\SystemModule;
use app\ddd\Admin\Domain\Module\SystemModuleRepository;
use ddd\Common\Domain\BaseService;
use ddd\Infrastructure\error\ZEntityNotExistsException;
use ddd\Infrastructure\error\ZException;

class ModuleService extends BaseService
{
    use SystemModuleRepository;

    /**
     * 删除模块
     * @param $id
     * @return bool|string
     * @throws \Exception
     */
    public function delModule($id) {
        $module = $this->getSystemModuleRepository()->findById($id);
        if(empty($module)){
            throw new ZEntityNotExistsException($id,"SystemModule");
        }
        if ($this->hasChildren($module))
        {
            throw new ZException('请删除当前模块的子模块');
        }
        $res = $this->getSystemModuleRepository()->delete($module);
        return true;
    }

    /**
     * 是否有子模块
     * @param SystemModule $module
     * @return bool
     * @throws \Exception
     */
    public function hasChildren(SystemModule $module) {
        $children = $this->getSystemModuleRepository()->findModulesByParentId($module->getId());
        if (\Utility::isNotEmpty($children)) {
            return true;
        }
        return false;
    }
}