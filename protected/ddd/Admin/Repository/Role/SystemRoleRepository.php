<?php
/**
 * This is Entity Repository for SystemRole.
 * Auto Generated.
 * DateTime: 2018-08-29 11:32:19
 * Describe：
 *
 */

namespace app\ddd\Admin\Repository\Role;

use app\ddd\Common\Domain\Value\Operator;

use app\ddd\Admin\Domain\Role\SystemRole;
use app\ddd\Admin\Domain\Role\ISystemRoleRepository;
use ddd\Common\Repository\EntityRepository;
use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;

class SystemRoleRepository extends EntityRepository implements ISystemRoleRepository
{
    public function getNewEntity() {
        return new SystemRole();
    }

    public function getActiveRecordClassName() {
        return "SystemRole";
    }


    /**
     * @param \SystemRole $model
     * @return SystemRole|\ddd\Common\Domain\BaseEntity
     * @throws \Exception
     */
    public function dataToEntity($model) {
        $entity = $this->getNewEntity();
        if (!empty($entity)) {
            $values = $model->getAttributes();
            $entity->setAttributes($values, false);
            $entity->setId($model->role_id);
            //$this->setEntityValue($entity, $model);

            $entity->create_user = new Operator($model->create_user_id, $model->create_user->name);
            $entity->update_user = new Operator($model->update_user_id, $model->update_user->name);
        }
        return $entity;
    }

    /**
     * @param SystemRole $entity
     * @return SystemRole
     * @throws \Exception
     */
    public function store($entity) {
        $id = $entity->getId();
        if (!empty($id)) {
            $model = $this->model()->findByPk($id);
            if (empty($model)) {
                throw new ZModelNotExistsException($id, $this->getActiveRecordClassName());
            }
        } else {
            //$this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new \SystemRole();
        }
        //这里需要处理一下新增时设置主键值的问题
        $entityAttributes=$entity->getAttributes();
        unset($entityAttributes['create_time']);
        $model->setAttributes($entityAttributes);
        if ($model->isNewRecord) {
            $model->create_user_id = $entity->create_user->id;
            if (empty($entity->create_time))
                $model->create_time = new \CDbExpression("now()");
        }
        $model->update_user_id = $entity->update_user->id;
        $model->update_time = new \CDbExpression("now()");
        if (!$model->save())
            throw new ZModelSaveFalseException($model);
        $entity->setId($model->getPrimaryKey());
        return $entity;
    }

    public function findAll($condition = '', $params = array()) {
        $res = parent::findAll();
        return $res;
    }

    /**
     * @desc 删除角色
     * @param $id
     * @return bool
     *
     */
    public function delRole($id) {
        $model = \SystemRole::model()->findById($id);
        if (empty($model)) {
            return false;
        }
        $res = $model->delete();
        if ($res == 1) {
            //删除用户角色关联
            $relations = \UserRoleRelation::model()->findAll([
                "condition" => "role_id=" . $id
            ]);
            foreach ($relations as $relation) {
                $relation->delete();
            }
            //删除用户权限关联
            $rightRelation = \SystemRoleRight::model()->deleteByPk($id);
            return true;
        } else
            return false;
    }

    /**
     * @desc 根据角色名 获取角色
     * @param $name
     */
    public function findByName($name){
        return $this->find('t.name=:name',[':name'=>$name]);
    }
}
