<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/24 14:44
 * Describe：
 */

namespace app\ddd\Admin\Domain\Right;


use app\ddd\Admin\Domain\User\SystemUser;
use ddd\Common\Domain\BaseService;
use ddd\Infrastructure\error\ZEntityNotExistsException;

class RightService extends BaseService
{
    use UserRightRepository;
    use RoleRightRepository;

    /**
     * 根据角色设置用户权限
     * @param    int $userId
     * @param    string $roleIds
     * @throws \Exception
     */
    public function setUserRightsWithRoles($userId, $roleIds)
    {
        // TODO: implement
        $userRight=$this->getUserRightRepository()->findById($userId);
        if(empty($userRight))
            $userRight=UserRight::create($userId);
//            throw new ZEntityNotExistsException($userId,SystemUser::class);

        $userRight->clearModuleActions();

        $roleRights=$this->getRoleRightRepository()->findByIds($roleIds);
        if(is_array($roleRights))
        {
            foreach ($roleRights as $roleRight)
            {
                $userRight->addModuleActions($roleRight->getModuleActions());
            }
        }
        $this->getUserRightRepository()->store($userRight);
    }

}