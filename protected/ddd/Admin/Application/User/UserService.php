<?php
/**
 * Created by youyi000.
 * DateTime: 2018/9/3 15:06
 * Describe：
 */

namespace app\ddd\Admin\Application\User;


use app\ddd\Admin\Domain\User\SystemUser;
use app\ddd\Admin\Domain\User\SystemUserRepository;
use app\ddd\Admin\Domain\User\UserRoleService;
use app\ddd\Admin\Repository\CacheDependency;
use app\ddd\Common\Repository\RedisCache;
use CDbExpression;
use ddd\Admin\Domain\User\SystemUserService;
use ddd\Admin\DTO\User\SystemUserDTO;

use ddd\Common\Application\TransactionService;

use ddd\Common\Domain\Value\DateTime;
use ddd\Infrastructure\error\ZEntityNotExistsException;
use ddd\Infrastructure\error\ZException;

class UserService extends TransactionService
{
    use SystemUserRepository;
    use RedisCache;

    /**
     * 清除依赖缓存
     */
    public function clearDependencyCache() {
        CacheDependency::clearDependencyCache(CacheDependency::USER);
    }

    /**
     * 获取用户主角色id
     * @param $userId
     * @return int
     */
    public function getUserMainRoleId($userId = 0) {
        if (empty($userId)) {
            $userId = \Mod::app()->user->id;
        }

        if (empty($userId))
            return 0;

        try {
            $user = $this->getUser($userId);
            if (!empty($user))
                return $user->main_role->id;
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * 获取用户当前主角色
     * @param int $userId
     * @return int
     */
    public function getUserNowMainRoleId($userId = 0) {
        if (empty($userId)) {
            $userId = \Mod::app()->user->id;
        }

        if (empty($userId))
            return 0;
        try {
            return UserRoleService::service()->getUserNowMainRoleId($userId);
        } catch (\Exception $e) {
            return 0;
        }

    }

    /**
     * 获取用户
     * @param $userId
     * @return \app\ddd\Admin\Domain\User\SystemUser
     * @throws \Exception
     */
    public function getUser($userId) {
        return $this->getSystemUserRepository()->findById($userId);
    }


    /**
     * 获取用户的所有角色
     * @param int $userId
     * @return array
     */
    public function getUserRoles($userId = 0) {
        if (empty($userId)) {
            $userId = \Mod::app()->user->id;
        }

        if (empty($userId))
            return [];

        try {
            $user = $this->getUser($userId);
            if (!empty($user))
                return $user->getRoles();
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 设置用户主角色
     * @param $roleId
     * @param int $userId
     * @throws \Exception
     */
    public function setMainRoleId($roleId, $userId = 0) {
        $user = $this->getUser($userId);
        if (empty($user))
            throw new ZException("用户不存在");

        $user->setMainRoleId($roleId, true);
    }

    /**
     * 变更用户当前主角色
     * @param $roleId
     * @param int $userId
     * @throws \Exception
     */
    public function changeMainRoleId($roleId, $userId = 0) {
        if (empty($userId)) {
            $userId = \Mod::app()->user->id;
        }

        if (empty($userId))
            throw new ZException("当前用户不存在");

        UserRoleService::service()->changeUserNowMainRoleId($userId, $roleId);

    }


    /**
     * @desc 获取用户信息详情
     * @param $userId
     * @return array
     * @throws \Exception
     */
    public function getUserDetail($userId) {
        $user = $this->getUser($userId);
        $userDTO = new SystemUserDTO();
        $userDTO->fromEntity($user);
        $userInfo = $userDTO->getAttributes();
        unset($userInfo['password']);
        unset($userInfo['confirmPassword']);
        return $userInfo;
    }

    /**
     * @desc 保存用户信息
     */
    public function save(SystemUser $userEntity) {
        try {

//            if ($userEntity->isCanEdit() !== true) {//不能编辑
//                ExceptionService::throwBusinessException(BusinessError::SystemModule_Can_Not_Edit, ['id' => $userEntity->getId()]);
//            }

            $this->beginTransaction();

            SystemUserService::service()->saveUser($userEntity);

            $this->commitTransaction();
            return true;
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            return $e->getMessage();
        }
    }


    /**
     * 删除用户
     * @param $id
     * @return int|string
     */
    public function delUser($id) {
        if (empty($id))
            return "id不能为空！";
        if (!\Utility::isIntString($id))
            return "非法Id";
        $res = $this->getSystemUserRepository()->delUser($id);
        return $res ? $res : '操作失败';
    }

    /**
     * @desc 更新登录信息
     */
    public function updateLoginInfo($userId) {
        $userEntity = $this->getSystemUserRepository()->findById($userId);
        if (empty($userEntity)) {
            throw new ZEntityNotExistsException($userId, "SystemUser");
        }
        $userEntity->login_time = new DateTime();
        $userEntity->login_count++;
        $this->getSystemUserRepository()->store($userEntity);
    }

    /**
     * @desc 修改密码
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function updatePwd($params) {
        $user = $this->getSystemUserRepository()->findById(\Utility::getNowUserId());
        if (empty($user->user_id))
            throw new ZException('当前用户不存在');
        if ($user->password != \Utility::getSecretPassword($params["password"]))
            throw new ZException('原密码不正确');

        $user->password = \Utility::getSecretPassword($params["newPassword"]);
        $user->update_time = new CDbExpression("now()");
        $res = $this->getSystemUserRepository()->store($user);
        return $res;
    }
}