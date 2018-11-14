<?php
/**
 * This is Entity Trait Repository for ModuleActionTree.
 * Auto Generated.
 * DateTime: 2018-08-29 10:03:15
 * Describe：
 *
 */

namespace app\ddd\Admin\Domain\Module;


use ddd\Infrastructure\DIService;

trait ModuleActionTreeRepository
{
    /**
    * @var IModuleActionTreeRepository
    */
    protected $moduleActionTreeRepository;

    /**
    * 获取项目仓储
    * @return IModuleActionTreeRepository
    * @throws \Exception
    */
    protected function getModuleActionTreeRepository()
    {
        if (empty($this->moduleActionTreeRepository))
        {
            $this->moduleActionTreeRepository=DIService::getRepository(IModuleActionTreeRepository::class);
        }
        return $this->moduleActionTreeRepository;
    }
}