<?php
/**
 * This is Entity Trait Repository for SystemModule.
 * Auto Generated.
 * DateTime: 2018-08-29 10:52:54
 * Describe：
 *
 */

namespace app\ddd\Admin\Domain\Module;


use ddd\Infrastructure\DIService;

trait SystemModuleRepository
{
    /**
    * @var ISystemModuleRepository
    */
    protected $systemModuleRepository;

    /**
    * 获取项目仓储
    * @return ISystemModuleRepository
    * @throws \Exception
    */
    protected function getSystemModuleRepository()
    {
        if (empty($this->systemModuleRepository))
        {
            $this->systemModuleRepository=DIService::getRepository(ISystemModuleRepository::class);
        }
        return $this->systemModuleRepository;
    }
}