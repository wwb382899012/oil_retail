<?php
/**
 * Created by youyi000.
 * DateTime: 2018/9/3 15:38
 * Describe：
 */

namespace app\ddd\Admin\Domain\User;


use app\ddd\Admin\Repository\CacheDependency;
use app\ddd\Cache\Application\CacheService;
use ddd\Common\Domain\BaseService;
use ddd\Infrastructure\error\ZEntityNotExistsException;
use ddd\Infrastructure\error\ZException;

class UserRoleService extends BaseService
{
    protected $cacheKeyPrefix="user_main_role_";

    protected $cacheService;

    use SystemUserRepository;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->cacheService=new CacheService();
    }

    protected function getCacheKey($userId)
    {
        return $this->cacheKeyPrefix.$userId;
    }

    /**
     * 获取用户主角色
     * @param $userId
     * @return int
     * @throws \Exception
     */
    public function getUserNowMainRoleId($userId)
    {
       $res= $this->cacheService->getCacheValue($this->getCacheKey($userId));
       if($res===false)
       {
            $roleId=$this->setUserNowMainRoleId($userId);
            return $roleId;
       }
       return $res;
    }

    /**
     * @param $userId
     * @return int
     * @throws \Exception
     */
    public function setUserNowMainRoleId($userId)
    {
        $user=$this->getSystemUserRepository()->findById($userId);
        if(empty($user))
        {
            throw new ZEntityNotExistsException($userId, SystemUserRepository::class);
        }
        $this->cacheService->setCache($this->getCacheKey($userId),$user->main_role->id,360000,CacheDependency::USER);
        return $user->main_role->id;
    }

    /**
     * 变更用户当前角色
     * @param $userId
     * @param $roleId
     * @throws \Exception
     */
    public function changeUserNowMainRoleId($userId,$roleId)
    {
        $user=$this->getSystemUserRepository()->findById($userId);
        if(empty($user))
        {
            throw new ZEntityNotExistsException($userId, SystemUserRepository::class);
        }
        if($user->hasRole($roleId))
            $this->cacheService->setCache($this->getCacheKey($userId),$roleId,360000,CacheDependency::MAIN_ROLE);
        else
            throw new ZException("当前用户没有所选角色");
    }
}