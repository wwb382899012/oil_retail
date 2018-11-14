<?php

use ddd\Infrastructure\DIService;
use app\ddd\Admin\Application\User\UserService;
use app\ddd\Admin\Domain\User\SystemUser;
use ddd\Infrastructure\error\ZEntityNotExistsException;

class UserUtility{

    const ADMIN_ROLE_ID = 1;

    /**
     * 获取当前用户信息
     * @return SystemUser
     * @throws Exception
     */
    public static function getNowUser():SystemUser{
        $entity = DIService::get(UserService::class)->getUser(Utility::getNowUserId());
        if(empty($entity)){
            throw new ZEntityNotExistsException(Utility::getNowUserId(),SystemUser::class);
        }

        return $entity;
    }

    /**
     * 获取当前用户ID
     * @return int
     */
    public static function getNowUserId():int{
        return (int) Mod::app()->user->id;
    }

    /**
     * 获取当前用户名
     * @return string
     * @throws Exception
     */
    public static function getNowUserName():string {
        $userInfo = self::getNowUser();
        return $userInfo->name;
    }

    /**
     * @desc 获取当前用户所有角色
     * @return array
     */
    public static function getNowUserRoles():array {
        $userId = Utility::getNowUserId();
        $roles =  UserService::service()->getUserRoles($userId);
        $res = array();
        if(Utility::isNotEmpty($roles)){
            foreach($roles as $key => $roleEntity){
                $res[$roleEntity->id] = $roleEntity->name;
            }
        }
        return $res;
    }

    /**
     * @desc 获取当前用户所有角色ids
     * @return array
     */
    public static function getNowUserRoleIds():array {
        $userId = Utility::getNowUserId();
        $roles =  UserService::service()->getUserRoles($userId);
        $res = array();
        if(Utility::isNotEmpty($roles)){
            foreach($roles as $key => $roleEntity){
                $res[] = $roleEntity->id;
            }
        }
        return $res;
    }

    /**
     * 判断用户是否是管理员
     * @param array $roleIds
     * @return bool
     */
    public static function isAdminUser(array $roleIds){
        if(in_array(static::ADMIN_ROLE_ID,$roleIds)){
            return true;
        }
        return false;
    }

    /**
     * 判断当前用户是否是管理员
     * @return bool
     */
    public static function isAdminOfCurrentUser():bool{
        return static::isAdminUser(static::getNowUserRoleIds());
    }

}