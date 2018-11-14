<?php
/**
 * This is Entity Trait Repository for UserMenu.
 * Auto Generated.
 * DateTime: 2018-08-28 16:02:21
 * Describe：
 *
 */

namespace app\ddd\Admin\Domain\Menu;


use ddd\Infrastructure\DIService;

trait UserMenuRepository
{
    /**
    * @var IUserMenuRepository
    */
    protected $userMenuRepository;

    /**
    * 获取项目仓储
    * @return IUserMenuRepository
    * @throws \Exception
    */
    protected function getUserMenuRepository()
    {
        if (empty($this->userMenuRepository))
        {
            $this->userMenuRepository=DIService::getRepository(IUserMenuRepository::class);
        }
        return $this->userMenuRepository;
    }
}