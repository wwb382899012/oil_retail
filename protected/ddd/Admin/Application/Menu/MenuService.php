<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 15:32
 * Describe：
 */

namespace app\ddd\Admin\Application\Menu;


use app\ddd\Admin\Domain\Menu\UserMenuRepository;
use app\ddd\Admin\DTO\Menu\MenuDTO;
use app\ddd\Admin\Repository\CacheDependency;
use app\ddd\Common\Repository\RedisCache;
use ddd\Common\Application\BaseService;

class MenuService extends BaseService
{
    use RedisCache;
    use UserMenuRepository;

    /**
     * 获取用户菜单
     * @param int $userId
     * @return MenuDTO|null
     * @throws \Exception
     */
    public function getUserMenu($userId=0)
    {
        if(empty($userId))
        {
            $userId=\Mod::app()->user->id;
        }
        if(empty($userId))
            return null;

        $menuDTO=$this->getEntityFromCache($userId);
//        $menuDTO=false;//TODO
        if($menuDTO===false)
        {
            $menu=$this->getUserMenuRepository()->findById($userId);
            $menuDTO=MenuDTO::createFromMenu($menu->menu);
            $this->setCache($userId,$menuDTO,360000,CacheDependency::getDependency(CacheDependency::ADMIN));
        }
        return $menuDTO;
    }
}