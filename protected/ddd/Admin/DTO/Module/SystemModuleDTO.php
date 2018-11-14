<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/27 17:55
 * Describe：
 */

namespace app\ddd\Admin\DTO\Module;


use app\ddd\Admin\Domain\Menu\Menu;
use app\ddd\Admin\Domain\Module\SystemModule;
use app\ddd\Admin\Domain\Module\SystemModuleRepository;
use app\ddd\Admin\DTO\Menu\MenuDTO;
use app\ddd\Common\Domain\Value\Operator;
use ddd\Common\Application\BaseDTO;


class SystemModuleDTO extends BaseDTO
{
    use SystemModuleRepository;

    #region property

    /**
     * 标识
     * @var   int
     */
    public $id;

    /**
     * 模块名称
     * @var   int
     */
    public $name;

    /**
     * 权限码
     * @var   int
     */
    public $code;

    /**
     * 图标
     * @var   int
     */
    public $icon;

    /**
     * 系统id
     * @var   int
     */
    public $system_id;

    /**
     * 父模块id
     * @var   int
     */
    public $parent_id;

    public $parent_name;
    /**
     * 父模块路径
     * @var   int
     */
    public $parent_ids;

    /**
     * 模块地址
     * @var   int
     */
    public $page_url;

    /**
     * 模块操作
     * @var   array
     */
    public $actions;

    /**
     * 排序码
     * @var   int
     */
    public $order_index;

    /**
     * 是否分开
     * @var   int
     */
    public $is_public;

    /**
     * 是否外部链接
     * @var   int
     */
    public $is_external;

    /**
     * 是否菜单
     * @var   int
     */
    public $is_menu;

    /**
     * 备注
     * @var   int
     */
    public $remark;

    /**
     * 状态
     * @var   int
     */
    public $status;

    public $update_time;

    /**
     * rules
     */
    public function rules() {
        return [
            ['name', "required", "message" => "模块名称不得为空"],
            ['code', "required", "message" => "权限码不得为空"],
            ['code', "validateCode"],
        ];
    }

    /**
     * 校验权限码
     * @param $attribute
     * @param $params
     */
    public function validateCode($attribute, $params) {
        $module = $this->getSystemModuleRepository()->findByCode($this->code);
        if (!empty($module) && $module->getId() != $this->id) {
            $this->addError($attribute, '当前权限码的模块已经存在，请重新填写！');
        }
    }

    #endregion

    public function fromEntity($entity) {
        $this->setAttributes($entity->getAttributes());
        if ($this->parent_id) {
            $systemModule = $this->getSystemModuleRepository()->findById($this->parent_id);
            $this->parent_name = $systemModule['name'];
        }
    }

    /**
     * 根据菜单实体创建DTO
     * @return SystemModule
     * @throws \Exception
     */
    public function toEntity() {
        $entity = SystemModule::create();
        $attributes=$this->getAttributes();
        $entity->setAttributes($attributes);
        $entity->addActions($attributes['actions']);
        $entity->create_user = new Operator(\Utility::getNowUserId(), \Utility::getNowUserName());
        $entity->update_user = $entity->create_user;
        //var_dump($entity);
        return $entity;
    }


    /**
     * 对DTO进行赋值
     * @param array $params
     * @throws \Exception
     */
    public function assignDTO(array $params) {
        $this->setAttributes($params);
    }
}