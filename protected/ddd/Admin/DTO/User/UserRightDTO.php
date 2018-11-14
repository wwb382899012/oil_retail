<?php
/**
 * User: liyu
 * Date: 2018/9/12
 * Time: 14:42
 * Desc: UserRightDTO.php
 */

namespace ddd\Admin\DTO\User;


use app\ddd\Admin\Domain\Menu\Menu;
use app\ddd\Admin\Domain\Right\UserRight;
use app\ddd\Admin\DTO\Menu\MenuDTO;
use app\ddd\Common\Domain\Value\Operator;
use ddd\Admin\DTO\Module\ModuleActionDTO;
use ddd\Common\Application\BaseDTO;

class UserRightDTO extends BaseDTO
{
    public $user_id;

    public $modules = [];

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
        $userRight = new UserRight();
        $userRight->setAttributes($this->getAttributes());
        $userRight->create_user = new Operator(\Utility::getNowUserId(), \Utility::getNowUserName());
        $userRight->update_user = $userRight->create_user;
        if (\Utility::isNotEmpty($this->modules)) {
            foreach ($this->modules as $module) {
                $mEntity=$module->toEntity();
                $userRight->addModuleAction($mEntity);
            }
        }
        return $userRight;
    }

    /**
     * 对DTO进行赋值
     * @param array $params
     * @throws \Exception
     */
    public function assignDTO(array $params) {
        $this->setAttributes($params);
        if (\Utility::isNotEmpty($params['user_right'])) {
            foreach ($params['user_right'] as $userRight) {
                $moduleDto = new ModuleActionDTO();
                $moduleDto->assignDTO($userRight);
                $this->modules[] = $moduleDto;
            }

        }
    }

}