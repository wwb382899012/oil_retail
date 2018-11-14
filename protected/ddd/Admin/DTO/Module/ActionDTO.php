<?php
/**
 * User: liyu
 * Date: 2018/9/12
 * Time: 15:16
 * Desc: ActionDTO.php
 */

namespace ddd\Admin\DTO\Module;


use app\ddd\Admin\Domain\Module\Action;

class ActionDTO
{
    #region property

    /**
     * 操作名称
     * @var   string
     */
    public $name;

    /**
     * 操作码
     * @var   string
     */
    public $code;

    public function toEntity() {
        $action = new Action($this->name, $this->code);
        return $action;
    }
}