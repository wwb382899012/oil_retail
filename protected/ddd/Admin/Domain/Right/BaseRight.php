<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/24 16:51
 * Describe：
 */

namespace app\ddd\Admin\Domain\Right;


use app\ddd\Admin\Domain\Module\ModuleAction;
use ddd\Common\Domain\BaseEntity;
use ddd\Infrastructure\error\ZException;

abstract class BaseRight extends BaseEntity
{

    /**
     * 权限
     * @var   ModuleAction[]
     */
    protected $modules=[];

    /**
     * 添加权限数组
     * @param ModuleAction[] $moduleActions
     * @throws \Exception
     */
    public function addModuleActions(array $moduleActions)
    {
        if(is_array($moduleActions))
        {
            foreach ($moduleActions as $item)
                $this->addModuleAction($item);
        }

    }

    /**
     * 设置权限
     * @param array $moduleActions
     * @throws \Exception
     */
    public function setModuleActions(array $moduleActions)
    {
        $this->clearModuleActions();
        $this->addModuleActions($moduleActions);
    }

    /**
     * 获取权限数组
     * @return ModuleAction[]
     */
    public function getModuleActions():array 
    {
        return array_values($this->modules);
    }

    /**
     * 获取模块操作
     * @param $moduleCode
     * @return ModuleAction|null
     */
    public function getModuleAction($moduleCode)
    {
        if(empty($moduleCode))
            return null;
        if(key_exists($moduleCode,$this->modules))
            return $this->modules[$moduleCode];
        else
            return null;
    }

    /**
     * 清除所有权限
     */
    public function clearModuleActions()
    {
        $this->modules=[];
    }

    /**
     * 移除指定权限码的权限
     * @param $code
     */
    public function removeModuleAction($code)
    {
        if(key_exists($code,$this->modules))
            unset($this->modules[$code]);
    }

    /**
     * 添加权限
     * @param ModuleAction $moduleAction
     * @throws \Exception
     */
    public function addModuleAction(ModuleAction $moduleAction)
    {
        if(empty($moduleAction->code))
            throw new ZException("模块权限码不得为空");
        if(key_exists($moduleAction->code,$this->modules))
        {
            $this->modules[$moduleAction->code]->addActions($moduleAction->getActions());
        }
        else
            $this->modules[$moduleAction->code]=$moduleAction;
    }

    public function customAttributeNames()
    {
        return ["moduleActions"];
    }

    /**
     * 是否指定模块的指定操作权限，如果$actionCode为空，则只判断是否有模块权限
     * @param $moduleCode
     * @param $actionCode
     * @return bool
     */
    public function hasRight($moduleCode,$actionCode=null)
    {
        if(empty($moduleCode))
            return false;
        if(key_exists($moduleCode,$this->modules))
        {
            if(empty($actionCode))
                return true;
            return $this->modules[$moduleCode]->hasRight($actionCode);
        }
        return false;
    }

}