<?php
/**
 * This is Entity Repository for RoleRight.
 * Auto Generated.
 * DateTime: 2018-08-24 16:17:41
 * Describe：
 *
 */

namespace app\ddd\Admin\Repository\Right;

use app\ddd\Admin\Repository\CacheDependency;
use app\ddd\Admin\Domain\Right\RoleRight;
use app\ddd\Admin\Domain\Right\IRoleRightRepository;
use app\ddd\Common\Domain\Value\Operator;
use ddd\Common\Repository\EntityRepository;

use ddd\Infrastructure\error\ZModelSaveFalseException;

class RoleRightRepository extends EntityRepository implements IRoleRightRepository
{
    public function getNewEntity()
    {
        return new RoleRight();
    }

    public function getActiveRecordClassName()
    {
        return "SystemRoleRight";
    }


    /**
     * @param \SystemRoleRight $model
     * @return RoleRight|\ddd\Common\Domain\BaseEntity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        if (!empty($entity))
        {
            $values=$model->getAttributes();
//            unset($values["actions"]);
            $entity->setAttributes($values, false);
            //$this->setEntityValue($entity, $model);
            $entity->update_user=new Operator($model->update_user_id,$model->update_user->name);
            $entity->addModuleActions($model->getRightCodes());
        }
        return $entity;
    }
    /**
     * @param $ids
     * @return RoleRight[]
     */
    public function findByIds($ids)
    {
        if(empty($ids))
            return null;
        // TODO: Implement findByIds() method.
        return $this->findAll("role_id in(".$ids.")");
    }


    /**
     * @param RoleRight $entity
     * @return RoleRight
     * @throws \Exception
     */
    public function store($entity)
    {
        $id = $entity->getId();
        if (!empty($id)) {
            $model = $this->model()->findByPk($id);
            if (empty($model)) {
                $this->activeRecordClassName = $this->getActiveRecordClassName();
                $model = new $this->activeRecordClassName;
                $model->role_id = $entity->role_id;
            }
        }
        //这里需要处理一下新增时设置主键值的问题
        $values=$entity->getAttributes();
        $model->right_codes=json_encode($values["moduleActions"]);
        if ($model->isNewRecord) {
            $model->create_user_id = $entity->create_user->id;
            if (empty($entity->create_time))
                $model->create_time = new \CDbExpression("now()");
        }
        $model->update_user_id = $entity->update_user->id;
        $model->update_time = new \CDbExpression("now()");
        if (!$model->save())
            throw new ZModelSaveFalseException($model);

        $this->clearDependencyCache();
        return $entity;
    }

    /**
     * 清除依赖缓存
     */
    protected function clearDependencyCache()
    {
        CacheDependency::clearDependencyCache(CacheDependency::ADMIN);
    }

}
