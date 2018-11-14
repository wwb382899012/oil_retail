<?php
/**
 * Created by vector.
 * DateTime: 2018/9/11 16:27
 * Describe：客户
 */

namespace app\ddd\Customer\DTO;

use app\ddd\Common\Domain\Value\Attachment;
use app\ddd\Common\Domain\Value\LogisticsCompany;
use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\Customer\Domain\Customer;

class CustomerDTO extends BaseDTO
{
    #region property

    /**
     * 标识（编号）
     * @var      int
     */
    public $customer_id = 0;

    /**
     * 用户帐号 
     * @var   string
     */
    public $account;
    
    /**
     * 手机号 
     * @var   string
     */
    public $phone;

    /**
     * 短信验证码
     * @var string
     */
    public $code;
    
    /**
     * 微信标识
     * @var   
     */
    public $open_id;
    
    /**
     * 备注 
     * @var   string
     */
    public $remark;
    
    /**
     * 状态 
     * @var   int
     */
    protected $status;


    #endregion

    public function rules()
    {
        return [
            ["account","required"],
            ['phone','match','pattern'=>'/^1[345678]{1}\d{9}$/','message'=>'{attribute}请填写正确的手机号']
        ];
    }

    /**
     * @name:fromEntity
     * @desc:转换为DTO对象
     * @param:* @param BaseEntity $entity
     * @throws \Exception
     */
    public function fromEntity(BaseEntity $entity)
    {
        $values = $entity->getAttributes(["phone","account"]);
        $this->setAttributes($values);
        $this->customer_id    = $entity->getId();
        $this->status         = $entity->getStatus()->status;
        $this->status_name    = \Map::$v['driver_status'][$this->status];
        $this->logistics_id   = $entity->company->logistics_id;
        $this->logistics_name = $entity->company->name;
    }
}