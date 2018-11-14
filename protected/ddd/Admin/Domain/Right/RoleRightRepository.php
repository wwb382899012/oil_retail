<?php
/**
 * This is Entity Trait Repository for RoleRight.
 * Auto Generated.
 * DateTime: 2018-08-24 16:17:41
 * Describe：
 *
 */

namespace app\ddd\Admin\Domain\Right;


use ddd\Infrastructure\DIService;

trait RoleRightRepository
{
    /**
    * @var IRoleRightRepository
    */
    protected $roleRightRepository;

    /**
    * 获取项目仓储
    * @return IRoleRightRepository
    * @throws \Exception
    */
    protected function getRoleRightRepository()
    {
        if (empty($this->roleRightRepository))
        {
            $this->roleRightRepository=DIService::getRepository(IRoleRightRepository::class);
        }
        return $this->roleRightRepository;
    }
}