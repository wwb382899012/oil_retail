<?php
/**
 * This is Entity Trait Repository for UserRight.
 * Auto Generated.
 * DateTime: 2018-08-24 16:17:56
 * Describe：
 *
 */

namespace app\ddd\Admin\Domain\Right;


use ddd\Infrastructure\DIService;

trait UserRightRepository
{
    /**
    * @var IUserRightRepository
    */
    protected $userRightRepository;

    /**
    * 获取项目仓储
    * @return IUserRightRepository
    * @throws \Exception
    */
    protected function getUserRightRepository()
    {
        if (empty($this->userRightRepository))
        {
            $this->userRightRepository=DIService::getRepository(IUserRightRepository::class);
        }
        return $this->userRightRepository;
    }
}