<?php
/**
 * User: liyu
 * Date: 2018/9/7
 * Time: 14:48
 * Desc: SystemRoleDTO.php
 */

namespace ddd\Admin\DTO\Role;


use app\ddd\Admin\Domain\Role\SystemRole;
use app\ddd\Admin\Domain\Role\SystemRoleRepository;
use app\ddd\Common\Domain\Value\Operator;
use ddd\Common\Application\BaseDTO;

class SystemRoleDTO extends BaseDTO
{
    use SystemRoleRepository;

    public $role_id;

    public $name;

    public $status;

    public $status_name;

    public $remark;

    public $order_index;

    public function rules() {
        return [
            ['name', "required", "message" => "角色名称不得为空"],
            ['name', "validateName"],
        ];
    }

    /**
     * 校验角色名
     * @param $attribute
     * @param $params
     */
    public function validateName($attribute, $params) {
        $role = $this->getSystemRoleRepository()->findByName($this->name);
        if (!empty($role) && $role->getId() != $this->role_id) {
            $this->addError($attribute, '当前角色已经存在，不能重复添加！');
        }
    }

    /**
     * @name:fromEntity
     * @desc: 转换为DTO对象
     * @param:* @param BaseEntity $entity
     * @throw:
     * @return:void
     */
    public function fromEntity($entity) {
        $this->setAttributes($entity->getAttributes());
    }

    /**
     * @name:toEntity
     * @desc: 转换为实体对象
     * @param:
     * @throw:
     * @return:LogisticsCompany
     */
    public function toEntity() {
        $entity = new SystemRole();
        $entity->setAttributes($this->getAttributes());
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