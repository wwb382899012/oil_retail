<?php
/**
 * User: liyu
 * Date: 2018/9/12
 * Time: 15:15
 * Desc: ModuleActionDTO.php
 */

namespace ddd\Admin\DTO\Module;


use app\ddd\Admin\Domain\Module\Action;
use app\ddd\Admin\Domain\Module\ModuleAction;
use ddd\Common\Application\BaseDTO;

class ModuleActionDTO extends BaseDTO
{
    #region property

    /**
     * 模块id
     * @var   int
     */
    public $id = 0;

    /**
     * 模块名
     * @var   string
     */
    public $label = '';

    /**
     * 模块码
     * @var   string
     */
    public $code = '';

    /**
     * @var int 父级ID
     */
    public $parent_id = 0;

    /**
     * 操作
     * @var   Action[]
     */
    public $actions = [];

    public function toEntity() {
        $entity = new ModuleAction();
//        $entity->setAttributes($this->getAttributes());
        $entity->id=$this->id;
        $entity->name=$this->label;
        $entity->parent_id=$this->parent_id;
        $entity->code=$this->code;
        if(!empty($this->actions)){
            $entity->addActions($this->actions);
        }
        return $entity;
    }

    public function assignDTO($params) {
        $this->setAttributes($params);
    }
}