<?php
/**
 * User: liyu
 * Date: 2018/9/7
 * Time: 11:16
 * Desc: SystemUserDTOserDTO.php
 */

namespace ddd\Admin\DTO\User;


use app\ddd\Admin\Domain\User\SystemUser;
use app\ddd\Admin\Domain\User\SystemUserRepository;
use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Role;
use ddd\Common\Application\BaseDTO;
use Utility;

class SystemUserDTO extends BaseDTO
{
    use SystemUserRepository;

    public $user_id;

    public $user_name;

    public $password;

    public $role_array = [];

    public $main_role_id;

    public $main_role_name;

    public $confirmPassword;//确认密码

    public $identity;

    public $weixin = '';

    public $phone = '';

    public $email = '';

    public $status = 0;

    public $status_name = '';

    public $is_right_role = 0;

    public $name = '';

    public $remark = '';

    public $user_right = [];

    public function rules() {
        return [
            ['user_name,name,main_role_id,phone,status,email', "required"],
            ['name', "validateUserName"],
            ['password', "validatePassword"],
        ];
    }

    /**
     * 校验用户名
     * @param $attribute
     * @param $params
     */
    public function validateUserName($attribute, $params) {
        $user = $this->getSystemUserRepository()->findByUserName($this->user_name);
        if (!empty($user) && $user->getId() != $this->user_id) {
            $this->addError($attribute, '当前用户名的用户已经存在，请重新填写用户名！');
        }
    }

    /**
     * @desc 校验密码
     */
    public function validatePassword($attribute, $params) {

        if (!empty($this->password)) {
            if ($this->password != $this->confirmPassword) {
                $this->addError($attribute, '密码与确认密码不一致，请重新输入！');
            }
        } else {
            if (empty($this->user_id)) {
                $this->addError($attribute, '新增用户，密码不得为空，请输入密码！');
            }
        }

    }

    /**
     * 转换为DTO对象
     * @param SystemUser $entity
     * @throws \Exception
     */
    public function fromEntity(SystemUser $entity) {
        $this->setAttributes($entity->getAttributes());
        $roleObj = [];
        if (\Utility::isNotEmpty($entity->roles)) {
            foreach ($entity->roles as $role) {
                $roleObj[] = new Role($role->id, $role->name);
            }
            $this->role_array = $roleObj;
        }

        $this->main_role_id = $entity->main_role->id;
        $this->main_role_name = $entity->main_role->name;
    }

    /**
     * @name:toEntity
     * @desc: 转换为实体对象
     * @param:
     * @throw:
     * @return:LogisticsCompany
     */
    public function toEntity() {
        $entity = SystemUser::create($this->user_id);
        $attributes=$this->getAttributes();
        $password=$attributes['password'];
        unset($attributes['password']);
        $entity->setAttributes($attributes);
        $entity->main_role = new Role($this->main_role_id, $this->main_role_name);
        if(!empty($password)){
            $entity->password = Utility::getSecretPassword($password);
        }
//        if (\Utility::isNotEmpty($this->role_array)) {
        $entity->clearRoles();
        $entity->addRoles($this->role_array);
//        }
        $entity->create_user = new Operator(\Utility::getNowUserId(), \Utility::getNowUserName());
        $entity->update_user = $entity->create_user;
        return $entity;
    }


    /**
     * @name:assignDTO
     * @desc:对DTO进行赋值
     * @param:* @param array $params
     * @throw:
     * @return:void
     */
    public function assignDTO(array $params) {

        $this->setAttributes($params);
    }
}