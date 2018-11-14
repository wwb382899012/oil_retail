<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/24 11:47
 * Describe：
 */

namespace app\ddd\Admin\Domain\Module;


use app\ddd\Common\Domain\Value\Operator;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\ZException;

class SystemModule extends BaseEntity implements IAggregateRoot
{
    #region property

    /**
     * 标识
     * @var   int
     */
    public $id = 0;

    /**
     * 模块名称
     * @var   string
     */
    public $name;

    /**
     * 权限码
     * @var   string
     */
    public $code;

    /**
     * 图标
     * @var   string
     */
    public $icon;

    /**
     * 系统id
     * @var   int
     */
    public $system_id = 0;

    /**
     * 父模块id
     * @var   int
     */
    public $parent_id = 0;

    /**
     * 父模块路径
     * @var   string
     */
    public $parent_ids;

    /**
     * 模块地址
     * @var   string
     */
    public $page_url;

    /**
     * 模块操作
     * @var   Action[]
     */
    protected $actions=[];

    /**
     * 排序码
     * @var   int
     */
    public $order_index = 0;

    /**
     * 是否分开
     * @var   boolean
     */
    public $is_public;

    /**
     * 是否外部链接
     * @var   boolean
     */
    public $is_external;

    /**
     * 是否菜单
     * @var   boolean
     */
    public $is_menu = true;

    /**
     * 备注
     * @var   string
     */
    public $remark;

    /**
     * 状态
     * @var   int
     */
    public $status = 0;

    /**
     * 创建时间
     * @var   \Datetime
     */
    public $create_time;

    /**
     * 更新用户
     * @var   Operator
     */
    public $update_user;

    /**
     * 更新时间
     * @var   \Datetime
     */
    public $update_time;

    /**
     * 创建用户
     * @var   Operator
     */
    public $create_user;

    #endregion

    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->id;
    }

    public function setId($value)
    {
        // TODO: Implement setId() method.
        $this->id=$value;
    }

    public function customAttributeNames()
    {
        return ["actions"];
    }

    public static function create(){
        $entity = new static();
        return $entity;
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

    public function isCanEdit()
    {
        return $this->status <= 1;
    }

}