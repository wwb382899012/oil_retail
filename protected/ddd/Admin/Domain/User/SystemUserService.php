<?php
/**
 * User: liyu
 * Date: 2018/9/13
 * Time: 11:28
 * Desc: SystemUserService.php
 */

namespace ddd\Admin\Domain\User;


use app\ddd\Admin\Domain\Right\RightService;
use app\ddd\Admin\Domain\User\SystemUserRepository;
use ddd\Common\Domain\BaseService;
use app\ddd\Admin\Domain\User\SystemUser;

class SystemUserService extends BaseService
{
    use SystemUserRepository;

    /**
     * 保存用户信息
     * @param SystemUser $entity
     * @throws \Exception
     */
    public function saveUser(SystemUser $entity) {
        $this->getSystemUserRepository()->store($entity);
        $this->updateUserRightByRoleRight($entity);
    }

    /**
     * @desc 根据角色权限  变更用户权限
     * @param SystemUser $entity
     * @throws \Exception
     */
    public function updateUserRightByRoleRight(SystemUser $entity){
        if ($entity->is_right_role == 1) {//根据用户角色变更权限
            $roles = $entity->getRoles();
            $rolesArr = [];
            if (\Utility::isNotEmpty($roles)) {
                foreach ($roles as $role){
                    $rolesArr[] = $role->id;
                }
            }
            $rolesArr[]=$entity->main_role->id;//合并主角色 和从角色
            $roleIds = !empty($rolesArr) ? implode(',', $rolesArr) : '';
            RightService::service()->setUserRightsWithRoles($entity->getId(), $roleIds);
        }
    }
}