<?php
/**
 * This is Entity Trait Repository for SystemRole.
 * Auto Generated.
 * DateTime: 2018-08-29 11:32:19
 * Describe：
 *
 */

namespace app\ddd\Admin\Domain\Role;


use ddd\Infrastructure\DIService;

trait SystemRoleRepository
{
    /**
    * @var ISystemRoleRepository
    */
    protected $systemRoleRepository;

    /**
    * 获取项目仓储
    * @return ISystemRoleRepository
    * @throws \Exception
    */
    protected function getSystemRoleRepository()
    {
        if (empty($this->systemRoleRepository))
        {
            $this->systemRoleRepository=DIService::getRepository(ISystemRoleRepository::class);
        }
        return $this->systemRoleRepository;
    }
}