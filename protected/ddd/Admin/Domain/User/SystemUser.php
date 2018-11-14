<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/24 11:48
 * Describe：
 */

namespace app\ddd\Admin\Domain\User;


use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Role;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\error\ZEntityNotExistsException;
use ddd\Infrastructure\error\ZException;

class SystemUser extends BaseEntity implements IAggregateRoot
{

    use SystemUserRepository;

    #region property

    /**
     * 标识
     * @var   int
     */
    public $user_id = 0;

    /**
     * 用户名
     * @var   string
     */
    public $user_name;

    /**
     * 密码
     * @var   string
     */
    public $password;

    /**
     * 主角色
     * @var   Role
     */
    public $main_role;

    /**
     * 用户角色
     * @var   Role[]
     */
    protected $roles=[];

    /**
     * 登录次数
     * @var   int
     */
    public $login_count = 0;

    /**
     * 登录时间
     * @var   \Datetime
     */
    public $login_time;

    /**
     * 唯一标识
     * @var   string
     */
    public $identity;

    /**
     * 微信
     * @var   string
     */
    public $weixin;

    /**
     * 电话
     * @var   string
     */
    public $phone;

    /**
     * 邮件
     * @var   string
     */
    public $email;

    /**
     * 公司主体
     * @var   Corporation[]
     */
    public $corps;

    public $is_right_role;

    /**
     * 姓名
     * @var   string
     */
    public $name;

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

    public static function create($userId=0){
        if($userId){
            $entity=DIService::getRepository(ISystemUserRepository::class)->findById($userId);
            if(empty($entity)){
                throw new ZEntityNotExistsException($userId,static::className());
            }
        }else{
            $entity=new static();
        }
        return $entity;
    }

    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->user_id;
    }

    public function setId($value)
    {
        // TODO: Implement setId() method.
        $this->user_id=$value;
    }

    public function customAttributeNames()
    {
        return ["roles"];
    }

    /**
     * @param Role[] $roles
     * @throws \Exception
     */
    public function addRoles(array $roles)
    {
        if(!is_array($roles))
            return;
        foreach ($roles as $role)
        {
            if(!($role instanceof Role)){
                $role=new Role($role['id'],$role['name']);
            }
            $this->addRole($role);
        }
    }

    /**
     * 添加角色
     * @param Role $role
     * @throws \Exception
     */
    public function addRole(Role $role)
    {
        if(empty($role->id))
            throw new ZException("角色id不得为空");
        $this->roles[$role->id]=$role;
    }

    public function clearRoles():void {
        $this->roles = [];
    }

    /**
     * 获取角色数组
     * @return Role[]
     */
    public function getRoles()
    {
        if(is_array($this->roles))
            return array_values($this->roles);
        else
            return [];
    }

    /**
     * 获取角色ids
     * @return string
     */
    public function getRoleIds()
    {
        $ids=[];
        foreach ($this->roles as $role)
            $ids[]=$role->id;
        return implode(",",$ids);
    }

    /**
     * 是否有当前角色
     * @param $roleId
     * @return bool
     */
    public function hasRole($roleId)
    {
        return key_exists($roleId,$this->roles);
    }

    /**
     * 保存用户主角色
     * @param $roleId
     * @param bool $persistent
     * @throws \Exception
     */
    public function setMainRoleId($roleId,$persistent=true)
    {
        if(!$this->hasRole($roleId))
            throw new ZException("用户无当前角色");

        $this->main_role=$this->roles[$roleId];
        if($persistent)
        {
            $this->getSystemUserRepository()->saveMainRoleId($this);
        }
    }



}