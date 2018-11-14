<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/27 17:55
 * Describe：
 */

namespace app\ddd\Admin\Application\Module;


use app\ddd\Admin\Domain\Module\ModuleActionTree;
use app\ddd\Admin\Domain\Module\ModuleActionTreeRepository;
use app\ddd\Admin\Domain\Module\SystemModuleRepository;
use app\ddd\Admin\DTO\Module\SystemModuleDTO;
use ddd\Common\Application\TransactionService;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;

class ModuleService extends TransactionService
{

    use ModuleActionTreeRepository;
    use SystemModuleRepository;

    /**
     * 获取有效的系统模块树
     * @return \app\ddd\Admin\Domain\Module\ModuleActionTree
     * @throws \Exception
     */
    public function getActiveModuleTree() {
        // TODO: implement
        $tree = $this->getModuleActionTreeRepository()->loadActive();

        return $tree;
    }

    /**
     * 获取所有的系统模块树
     * @return \app\ddd\Admin\Domain\Module\ModuleActionTree
     * @throws \Exception
     */
    public function getAllModuleTree() {
        // TODO: implement
        $tree = $this->getModuleActionTreeRepository()->load();

        return $tree;
    }

    public function formatTree(ModuleActionTree $tree) {

    }


    /**
     * @desc 保存模块信息
     * @param SystemModuleDTO $dto
     * @return array|bool|mixed|string
     */
    public function save(SystemModuleDTO $dto) {
        if (!$dto->validate()) {
            return $dto->getErrors();
        }
        try {
            $entity = $dto->toEntity();

            if ($entity->isCanEdit() !== true) {//不能编辑
                ExceptionService::throwBusinessException(BusinessError::SystemModule_Can_Not_Edit, ['id' => $entity->getId()]);
            }

            $this->beginTransaction();

            $this->getSystemModuleRepository()->store($entity);

            $this->commitTransaction();
            return true;
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            return $e->getMessage();
        }
    }

    /**
     * @desc 获取模块信息详情
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function getModuleDetail($id) {
        $module = $this->getSystemModuleRepository()->findById($id);
        $moduleDto = new SystemModuleDTO();
        $moduleDto->fromEntity($module);
        return $moduleDto->getAttributes();
    }


    /**
     * @desc 删除模块
     * @param $id
     * @return bool|string
     */
    public function delModule($id)
    {
        if (empty($id))
            return "id不能为空！";
        if (!\Utility::isIntString($id))
            return "非法id";

        try {
            $service=new \ddd\Admin\Domain\Module\ModuleService();
            $res = $service->delModule($id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $res;
    }
}