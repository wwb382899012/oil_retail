<?php
/**
 * This is Entity Trait Repository for SystemUser.
 * Auto Generated.
 * DateTime: 2018-08-29 11:29:03
 * Describe：
 *
 */

namespace app\ddd\Admin\Domain\User;



trait SystemUserRepository
{
    /**
    * @var ISystemUserRepository
    */
    protected $systemUserRepository;

    /**
    * 获取项目仓储
    * @return ISystemUserRepository
    * @throws \Exception
    */
    protected function getSystemUserRepository()
    {
        if (empty($this->systemUserRepository))
        {
            $this->systemUserRepository=\ddd\Infrastructure\DIService::getRepository(ISystemUserRepository::class);
        }
        return $this->systemUserRepository;
    }
}