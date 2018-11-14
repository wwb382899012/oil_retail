<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/27 11:08
 * Describe：
 */

namespace app\ddd\Admin\Application\Right;


use app\ddd\Admin\Domain\Right\RoleRightRepository;
use app\ddd\Admin\Domain\Right\UserRightRepository;
use ddd\Common\Application\BaseService;

class AuthorizeService extends BaseService
{

    use UserRightRepository;
    use RoleRightRepository;

    /**
     * 判断是否的操作权限
     * @param $userId
     * @param $moduleCode
     * @param $actionCode
     * @return bool
     * @throws \Exception
     */
    public function checkActionRight($moduleCode, $actionCode, $userId = 0) {
        if (empty($moduleCode))
            return false;
        if (empty($userId)) {
            $userId = \Mod::app()->user->id;
        }
        if (empty($userId))
            return false;

        $userRight = $this->getUserRight($userId);
        return $userRight->hasRight($moduleCode, $actionCode);
    }

    /**
     * 获取用户权限
     * @param $userId
     * @return \app\ddd\Admin\Domain\Right\UserRight
     * @throws \Exception
     */
    public function getUserRight($userId = 0) {
        if (empty($userId)) {
            $userId = \Mod::app()->user->id;
        }
        if (empty($userId))
            return null;
        return $this->getUserRightRepository()->findById($userId);
    }

    /**
     * 获取角色权限
     * @param $userId
     * @return \app\ddd\Admin\Domain\Right\UserRight
     * @throws \Exception
     */
    public function getRoleRight($roleId = 0) {
        if (empty($roleId))
            return null;
        return $this->getRoleRightRepository()->findById($roleId);
    }

    /**
     * 根据模块权限码返回操作权限数组
     * @param $moduleCode
     * @param int $userId
     * @return \app\ddd\Admin\Domain\Module\Action[]|null
     * @throws \Exception
     */
    public function getActionsWithModuleCode($moduleCode, $userId = 0) {
        if (empty($moduleCode))
            return null;
        if (empty($userId)) {
            $userId = \Mod::app()->user->id;
        }
        if (empty($userId))
            return null;


        $userRight = $this->getUserRight($userId);
        $moduleAction = $userRight->getModuleAction($moduleCode);
        if (!empty($moduleAction))
            return $moduleAction->getActions();
        return null;
    }

    /**
     * 根据模块权限码返回操作权限码数组
     * @param $moduleCode
     * @param int $userId
     * @return array|null
     * @throws \Exception
     */
    public function getActionCodesWithModuleCode($moduleCode, $userId = 0) {
        if (empty($moduleCode))
            return null;
        if (empty($userId)) {
            $userId = \Mod::app()->user->id;
        }
        if (empty($userId))
            return null;

        $userRight = $this->getUserRight($userId);
        $moduleAction = $userRight->getModuleAction($moduleCode);
        if (!empty($moduleAction))
            return $moduleAction->getActionCodes();
        return null;
    }

}