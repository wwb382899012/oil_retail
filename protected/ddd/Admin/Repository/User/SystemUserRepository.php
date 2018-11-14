<?php
/**
 * This is Entity Repository for SystemUser.
 * Auto Generated.
 * DateTime: 2018-08-29 11:29:03
 * Describe：
 *
 */

namespace app\ddd\Admin\Repository\User;

use app\ddd\Admin\Repository\CacheDependency;
use app\ddd\Common\Domain\Value\Role;
use app\ddd\Common\Repository\RedisCache;

use app\ddd\Admin\Domain\User\SystemUser;
use app\ddd\Admin\Domain\User\ISystemUserRepository;
use ddd\Common\Domain\Value\DateTime;
use ddd\Common\Repository\EntityRepository;
use ddd\Infrastructure\error\ZEntityNotExistsException;

use ddd\Infrastructure\error\ZModelNotExistsException;
use ddd\Infrastructure\error\ZModelSaveFalseException;

class SystemUserRepository extends EntityRepository implements ISystemUserRepository
{
    use RedisCache;

    public $with = ["roles"];

    public function getNewEntity() {
        return new SystemUser();
    }

    public function getActiveRecordClassName() {
        return \SystemUser::class;
    }

    /**
     * 清除依赖缓存
     */
    public function clearDependencyCache() {
        CacheDependency::clearDependencyCache(CacheDependency::USER);
    }

    /**
     * @param $id
     * @return SystemUser
     * @throws \Exception
     */
    public function findById($id):SystemUser {
        $entity = $this->getEntityFromCache($id);
        if ($entity === false) {
            $entity = parent::findById($id);
            if (!empty($entity)){
                $this->setCache($id, $entity, 370000, CacheDependency::getDependency(CacheDependency::USER));
            }
        }
        if(empty($entity)){
            throw new ZEntityNotExistsException($id,SystemUser::class);
        }

        return $entity;
    }

    /**
     * @param \SystemUser $model
     * @return SystemUser|\ddd\Common\Domain\BaseEntity
     * @throws \Exception
     */
    public function dataToEntity($model) {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(), false);
        //
        $entity->clearRoles();
        if (\Utility::isNotEmpty($model->roles)) {
            foreach ($model->roles as $item) {
                $role = new Role($item->role_id, $item->name);
                $entity->addRole($role);
            }
        }

        //主角色
        if(!empty($model->mainRole)){
            $entity->main_role=new Role($model->mainRole->role_id,$model->mainRole->name);
        }
        if(!empty($model->login_time)) {
            $entity->login_time = new DateTime($model->login_time);
        }
        return $entity;
    }

    /**
     * @param SystemUser $entity
     * @return SystemUser
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
            $model = new \SystemUser();
        }
        //这里需要处理一下新增时设置主键值的问题
        $values = $entity->getAttributes();
        unset($values["roles"]);
        unset($values["create_time"]);
        $model->setAttributes($values);
        $model->role_ids = $entity->getRoleIds();
        $model->main_role_id = $entity->main_role->id;
        if ($model->isNewRecord) {
            $model->create_user_id = $entity->create_user->id;
            if (empty($entity->create_time))
                $model->create_time = new \CDbExpression("now()");
        }
        $model->update_user_id = $entity->update_user->id;
        $model->update_time = new \CDbExpression("now()");
        if(!empty($entity->login_time)&&$entity->login_time instanceof DateTime){
            $model->login_time=$entity->login_time->toDateTime();
        }
        if (!$model->save())
//            $errors=$model->getErrors();
            throw new ZModelSaveFalseException($model);
        $entity->setId($model->getPrimaryKey());

        $roles = $entity->getRoles();
        //保存用户角色的关联关系
        $relations = \UserRoleRelation::model()->findAll([
            "condition" => "user_id=" . $model->user_id,
            "index" => "role_id",
        ]);
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (key_exists($role->id, $relations)) {
                    unset($relations[$role->id]);
                } else {
                    $relation = new \UserRoleRelation();
                    $relation->user_id = $model->user_id;
                    $relation->role_id = $role->id;
                    if (!$relation->save())
                        throw new ZModelSaveFalseException($relation);
                }
            }
        }

        foreach ($relations as $relation) {
            $relation->delete();
        }
        $this->clearCache($entity->getId());
        $this->clearDependencyCache();
        return $entity;
    }

    /**
     * 保存主角色
     * @param SystemUser $user
     * @throws \Exception
     */
    public function saveMainRoleId(SystemUser $user) {
        // TODO: Implement saveMainRoleId() method.
        $model = \SystemUser::model()->findByPk($user->getId());
        if (empty($model)) {
            throw new ZModelNotExistsException($user->getId(), "SystemUser");
        }

        $model->main_role_id = $user->main_role->id;
        $model->update_user_id = \Utility::getNowUserId();
        $model->update_time = new \CDbExpression("now()");

        $res = $model->update(array("main_role_id", "update_user_id", "update_time"));
        if (!$res)
            throw new ZModelSaveFalseException($model);
        $this->clearCache($user->getId());
        $this->clearDependencyCache();
    }


    /**
     * @desc 删除用户
     * @param $id
     * @return bool
     *
     */
    public function delUser($id) {
        $model = \SystemUser::model()->findByPk($id);
        if(empty($model)){
            return false;
        }
        $res=$model->delete();
        if ($res == 1) {
            //删除用户角色关联
            $relations = \UserRoleRelation::model()->findAll([
                "condition" => "user_id=" . $id
            ]);
            foreach ($relations as $relation) {
                $relation->delete();
            }
            //删除用户权限关联
            $rightRelation = \SystemUserRight::model()->deleteByPk($id);
            $this->clearCache($id);
            $this->clearDependencyCache();
            return true;
        } else
            return false;
    }

    /**
     * @desc 根据用户名 获取用户信息
     * @param $userName
     * @return \ddd\Common\Domain\BaseEntity|null
     */
    public function findByUserName($userName){
        return $this->find('t.user_name=:userName',[':userName'=>$userName]);
    }


    /**
     * @desc 获取所有根据角色变更权限的用户
     * @return array SystemUser
     */
    public function findAllUserIsRightRole($roleId) {
        return $this->findAll('t.is_right_role=1 AND (FIND_IN_SET('.$roleId.',t.role_ids) OR t.main_role_id='.$roleId.') AND t.status=1');
    }
}
