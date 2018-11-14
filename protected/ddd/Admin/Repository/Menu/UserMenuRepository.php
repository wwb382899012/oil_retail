<?php
/**
 * This is Entity Repository for UserMenu.
 * Auto Generated.
 * DateTime: 2018-08-28 16:02:21
 * Describe：
 */

namespace app\ddd\Admin\Repository\Menu;

use app\ddd\Admin\Domain\Menu\IUserMenuRepository;
use app\ddd\Admin\Domain\Menu\Menu;
use app\ddd\Admin\Domain\Menu\UserMenu;
use app\ddd\Admin\Domain\Right\UserRight;
use app\ddd\Admin\Domain\Right\UserRightRepository;
use app\ddd\Admin\Repository\CacheDependency;
use app\ddd\Common\Repository\RedisCache;
use ddd\Common\Repository\BaseRepository;


class UserMenuRepository extends BaseRepository implements IUserMenuRepository{
    use RedisCache;
    use UserRightRepository;

    /**
     * 缓存依赖项
     * @var array
     */
    protected $dependency = ["_admin_"];

    /**
     * @param int $id
     * @return UserMenu
     * @throws \CDbException
     * @throws \CException
     */
    public function findById(int $id):UserMenu{
        $entity = $this->getEntityFromCache($id);
        if(empty($entity)){
            $entity = new UserMenu();
            $entity->setId($id);

            $menu = new Menu();
            $menu->id = 0;

            $userRight = $this->getUserRightRepository()->findById($id);
            if(!empty($userRight)){
                $items = \SystemModule::model()->findAll([
                    "condition" => "is_menu=1 and status=1",
                    "order"     => "parent_id asc,order_index asc"
                ]);
                if(\Utility::isNotEmpty($items)){
                    $this->generateChildren($userRight, $menu, $items);
                }
            }

            $entity->menu = $menu;

            $this->setCache($id, $entity, 360000, CacheDependency::getDependency(CacheDependency::ADMIN));
        }

        return $entity;
    }

    /**
     * 生成子菜单
     * @param UserRight       $userRight
     * @param Menu            $menu
     * @param \SystemModule[] $items
     * @return Menu
     * @throws \Exception
     */
    protected function generateChildren(UserRight $userRight, Menu &$menu, array &$items){
        foreach($items as $k => $item){
            if($item->parent_id == $menu->getId()){
                if($userRight->hasRight($item->code)){
                    $child = new Menu();
                    $child->setAttributes($item->getAttributes());
                    $this->generateChildren($userRight, $child, $items);
                    $menu->addChild($child);
                }

                unset($items[$k]);
            }
        }
        return $menu;
    }
}
