<?php
/**
 * This is Entity Repository for SystemModule.
 * Auto Generated.
 * DateTime: 2018-08-29 10:52:54
 * Describe：
 *
 */

namespace app\ddd\Admin\Repository\Module;

use app\ddd\Admin\Repository\CacheDependency;
use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Repository\RedisCache;

use app\ddd\Admin\Domain\Module\SystemModule;
use app\ddd\Admin\Domain\Module\ISystemModuleRepository;
use ddd\Common\Repository\EntityRepository;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;

class SystemModuleRepository extends EntityRepository implements ISystemModuleRepository
{
    use RedisCache;

    /**
     * 清除依赖缓存
     */
    public function clearDependencyCache()
    {
        CacheDependency::clearDependencyCache(CacheDependency::MODULE);
    }

    public function getNewEntity()
    {
        return new SystemModule();
    }

    public function getActiveRecordClassName()
    {
        return "SystemModule";
    }

    /**
     * @param \SystemModule $model
     * @return SystemModule|\ddd\Common\Domain\BaseEntity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $values = $model->getAttributes();
        unset($values["actions"]);
        $entity->setAttributes($values, false);
        $entity->create_user = new Operator($model->create_user_id, $model->create_user->name);
        $entity->update_user = new Operator($model->update_user_id, $model->update_user->name);
        $actions = $model->getActions();
        if (\Utility::isNotEmpty($actions))
        {
            $entity->addActions($actions);
        } /*else {
            $entity->actions = [];
        }*/

        return $entity;
    }

    /**
     * @param SystemModule $entity
     * @return SystemModule
     * @throws \Exception
     */
    public function store($entity)
    {
        $id = $entity->getId();
        if (!empty($id))
        {
            $model = $this->model()->findByPk($id);
            if (empty($model))
            {
                throw new ZModelNotExistsException($id, $this->getActiveRecordClassName());
            }
        }
        else
        {
            //$this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new \SystemModule(); //$this->activeRecordClassName;
        }
        //这里需要处理一下新增时设置主键值的问题
        $entityAttributes = $entity->getAttributes();
        unset($entityAttributes['create_time']);
        $model->setAttributes($entityAttributes);
        $model->setActions($entity->getActions());

        if ($model->isNewRecord)
        {
            $model->create_user_id = $entity->create_user->id;
            if (empty($entity->create_time))
                $model->create_time = new \CDbExpression("now()");
        }
        $model->update_user_id = $entity->update_user->id;
        $model->update_time = new \CDbExpression("now()");
        if (!$model->save())
            throw new ZModelSaveFalseException($model);

        $this->clearCache($entity->getId());
        $this->clearDependencyCache();
        $entity->setId($model->getPrimaryKey());
        return $entity;
    }

    /**
     * @param SystemModule $module
     * @return bool
     * @throws \Exception
     */
    public function delete($module)
    {
        $rows = \SystemModule::model()->deleteByPk($module->getId());

        if ($rows == 1)
        {
            $this->clearCache($module->getId());
            $this->clearDependencyCache();
            return true;
        }
        else
            return false;
    }

    public function findModulesByParentId($parentId)
    {
        $modules = \SystemModule::model()->findAll('parent_id=' . $parentId);
        return $modules;
    }


    /**
     * 根据权限码 获取模块
     * @param $code
     * @return SystemModule|null
     * @throws \Exception
     */
    public function findByCode($code)
    {
        return $this->find('t.code=:code', [':code' => $code]);
    }
}
