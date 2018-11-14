<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/24 14:41
 * Describe：
 */

namespace app\ddd\Admin\Domain\Module;


use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\Infrastructure\error\ZException;

class ModuleAction extends BaseEntity
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
    public $name = '';

    /**
     * 模块码
     * @var   string
     */
    public $code = '';

    /**
     * @var int 父级ID
     */
    public $parent_id=0;

    /**
     * @var 状态
     */
    protected $status;

    /**
     * 操作
     * @var   Action[]
     */
    protected $actions=[];

    #endregion

    public function customAttributeNames()
    {
        return ["actions"];
    }


    /**
     * @param Action[] $actions
     * @throws \Exception
     */
    public function addActions(array $actions)
    {
        if(!is_array($actions))
            return;
        foreach ($actions as $action)
        {
            if(!($action instanceof Action)){
                $action=new Action($action['name'],$action['code']);
            }
            $this->addAction($action);
        }
    }

    /**
     * 添加操作权限
     * @param Action $action
     * @throws \Exception
     */
    public function addAction(Action $action)
    {
        if(empty($action->code))
            throw new ZException("操作码不得为空");
        $this->actions[$action->code]=$action;
    }

    /**
     * 获取操作权限数组
     * @return Action[]
     */
    public function getActions()
    {
        return array_values($this->actions);
    }

    public function setStatus(Status $status){
        $this->status=$status;
    }

    public function getStatus(){
        return $this->status;
    }

    /**
     * 获取操作码的数组
     * @return array
     */
    public function getActionCodes()
    {
        return array_keys($this->actions);
    }

    public function __sleep()
    {
        // TODO: Implement __sleep() method.
        return ["id","name","code","actions","status"];
    }

    /**
     * 是否有指定操作码的权限
     * @param $actionCode
     * @return bool
     */
    public function hasRight($actionCode)
    {
        if(empty($actionCode))
            return false;
        if(key_exists($actionCode,$this->actions))
            return true;
        else
            return false;
    }

}