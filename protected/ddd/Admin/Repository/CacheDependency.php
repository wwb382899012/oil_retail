<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 19:43
 * Describe：
 */

namespace app\ddd\Admin\Repository;


use app\ddd\Cache\Application\CacheService;
use ddd\Infrastructure\DIService;

class CacheDependency
{
    /**
     * 用户主角色的缓存依赖
     */
    const MAIN_ROLE="main_role";

    /**
     * 纯用户相关的依赖
     */
    const USER="_user_";

    /**
     * 权限、菜单依赖
     */
    const ADMIN="_admin_";


    /**
     * 模块依赖
     */
    const MODULE="_module_";

    public static $configs=[
        "main_role"=>["_user_"],
        "_admin_"=>["_admin_"],
        "_user_"=>["_user_","_admin_"],
        "_module_"=>["_module_","_admin_"],
    ];

    /**
     * 获取缓存依赖
     * @param $type
     * @return array
     */
    public static function getDependency($type)
    {
        if(empty($type))
            $type="_admin_";
        return self::$configs[$type];
    }

    /**
     * 清除缓存依赖
     */
    public static function clearDependencyCache($type)
    {
        try{
            $dependency=static::getDependency($type);
            $cacheService=DIService::get(CacheService::class);
            foreach ($dependency as $key)
                $cacheService->clearCache($key);
        }
        catch (\Exception $e)
        {

        }
    }
}