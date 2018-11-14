<?php
/**
 * User: liyu
 * Date: 2018/9/7
 * Time: 14:18
 * Desc: RoleService.php
 */

namespace ddd\Admin\Application\Role;


use app\ddd\Admin\Domain\Right\RoleRight;
use app\ddd\Admin\Domain\Right\RoleRightRepository;
use app\ddd\Admin\Domain\Role\SystemRole;
use app\ddd\Admin\Domain\Role\SystemRoleRepository;

use app\ddd\Admin\Domain\User\SystemUserRepository;
use app\ddd\Admin\Domain\User\UserRoleService;
use ddd\Admin\Domain\User\SystemUserService;
use ddd\Common\Application\TransactionService;

class RoleService extends TransactionService
{

    use SystemRoleRepository;
    use RoleRightRepository;
    use SystemUserRepository;

    /**
     * @desc 获取所有角色信息
     * @return mixed
     * @throws \Exception
     */
    public function getRoles() {
        return $this->getSystemRoleRepository()->findAll();
    }

    /**
     * @desc 保存角色信息
     * @param SystemRole $roleEntity
     * @return bool|string
     */
    public function save(SystemRole $roleEntity) {
        try {

//            if ($userEntity->isCanEdit() !== true) {//不能编辑
//                ExceptionService::throwBusinessException(BusinessError::SystemModule_Can_Not_Edit, ['id' => $userEntity->getId()]);
//            }

            $this->beginTransaction();
            $this->getSystemRoleRepository()->store($roleEntity);

            $this->commitTransaction();
            return true;
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            return $e->getMessage();
        }
    }

    /**
     * @desc 获取角色详细信息
     * @param $roleId
     * @return mixed
     * @throws \Exception
     */

    public function getRoleDetail($roleId) {
        return $this->getSystemRoleRepository()->findById($roleId);
    }

    /**
     * @desc 删除角色
     * @return string
     */
    public function delRole($roleId) {
        if (empty($roleId))
            return "roleId不能为空！";
        if (!\Utility::isIntString($roleId))
            return "非法roleId";
        $res = $this->getSystemRoleRepository()->delRole($roleId);
        return $res ? $res : '操作失败';
    }

    /**
     * @desc 保存角色授权
     * @param RoleRight $entity
     * @return bool|string
     */
    public function saveRoleRight(RoleRight $entity) {
        try {

            $this->beginTransaction();

            $this->getRoleRightRepository()->store($entity);
            $this->updateUserRightByRole($entity->getId());

            $this->commitTransaction();
            return true;
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            return $e->getMessage();
        }
    }


    /**
     * @desc 批量变更 用户的权限（选中根据角色变更权限的用户）
     * @throws \Exception
     */
    public function updateUserRightByRole($roleId) {
        $userWithRightRole = $this->getSystemUserRepository()->findAllUserIsRightRole($roleId);
        if (\Utility::isNotEmpty($userWithRightRole)) {
            foreach ($userWithRightRole as $item) {
                SystemUserService::service()->updateUserRightByRoleRight($item);
            }
        }
    }
}