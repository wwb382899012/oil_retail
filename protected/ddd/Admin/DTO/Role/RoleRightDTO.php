<?php
/**
 * User: liyu
 * Date: 2018/9/12
 * Time: 17:11
 * Desc: RoleRightDTO.php
 */

namespace ddd\Admin\DTO\Role;


use app\ddd\Admin\Domain\Menu\Menu;
use app\ddd\Admin\Domain\Right\RoleRight;
use app\ddd\Admin\DTO\Menu\MenuDTO;
use app\ddd\Common\Domain\Value\Operator;
use ddd\Admin\DTO\Module\ModuleActionDTO;
use ddd\Common\Application\BaseDTO;

class RoleRightDTO extends BaseDTO
{
    public $role_id;

    /**
     * @var ModuleActionDTO
     */

    public $modules = [];

    /**
     * @param Menu $menu
     * @throws \Exception
     */
    public function fromEntity(Menu $menu) {
        $this->setAttributes($menu->getAttributes());
        $children = $menu->getChildren();
        if (is_array($children)) {
            foreach ($children as $child) {
                $childMenuDTO = new MenuDTO();
                $childMenuDTO->fromEntity($child);
                $this->addChild($childMenuDTO);
            }
        }
    }

    public function toEntity() {
        $roleRight = new RoleRight();
        $roleRight->setAttributes($this->getAttributes());
        $roleRight->create_user = new Operator(\Utility::getNowUserId(), \Utility::getNowUserName());
        $roleRight->update_user = $roleRight->create_user;
        if (\Utility::isNotEmpty($this->modules)) {
            foreach ($this->modules as $module) {
                $mEntity = $module->toEntity();
                if(!empty($mEntity)){
                    $roleRight->addModuleAction($mEntity);
                }
            }
        }
        return $roleRight;
    }

    /**
     * 对DTO进行赋值
     * @param array $params
     * @throws \Exception
     */
    public function assignDTO(array $params) {
        $this->setAttributes($params);
        if (\Utility::isNotEmpty($params['role_right'])) {
            foreach ($params['role_right'] as $roleRight) {
                $moduleDto = new ModuleActionDTO();
                $moduleDto->assignDTO($roleRight);
                $this->modules[] = $moduleDto;
            }

        }
    }
}