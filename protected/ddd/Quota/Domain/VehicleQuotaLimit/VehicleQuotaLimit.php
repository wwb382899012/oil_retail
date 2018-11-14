<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/4 0004
 * Time: 16:04
 */

namespace ddd\Quota\Domain\VehicleQuotaLimit;


use app\ddd\Common\Domain\Value\Operator;
use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\Common\IAggregateRoot;
use ddd\Infrastructure\error\BusinessError;
use ddd\Infrastructure\error\ExceptionService;
use ddd\Infrastructure\IDService;

class VehicleQuotaLimit extends BaseEntity implements IAggregateRoot
{

    /**
     * 标识
     * @var   int
     */
    public $limit_id = 0;

    /**
     * 编号
     * @var   string
     */
    public $code;

    /**
     * 每日油箱占比
     * @var   float
     */
    public $rate = 0;

    /**
     * 备注
     * @var   string
     */
    public $remark;

    /**
     * 状态
     * @var   Status
     */
    protected $status;

    /**
     * 生效时间
     * @var   DateTime
     */
    public $effect_time;

    /**
     * 创建时间
     * @var   Datetime
     */
    public $create_time;

    /**
     * 更新用户
     * @var   Operator
     */
    public $update_user;

    /**
     * 更新时间
     * @var   Datetime
     */
    public $update_time;

    /**
     * 创建用户
     * @var   Operator
     */
    public $create_user;

    public function getIdName()
    {
        return 'limit_id';
    }

    public function getId()
    {
        return $this->limit_id;
    }

    public function setId($value)
    {
        $this->limit_id = $value;
    }

    /**
     * 获取车辆限额编号
     */
    protected function generateCode()
    {
        $this->code = 'CL' . IDService::getVehicleQuotaLimitCodeId();
    }

    /**
     * 创建
     * @param float $rate
     * @return   VehicleQuotaLimit
     * @throws \Exception
     */
    public static function create($rate)
    {
        if (empty($rate))
        {
            ExceptionService::throwBusinessException(BusinessError::Argument_Required, ['name' => 'rate']);
        }
        $entity = new static();
        $entity->generateCode();
        $entity->rate = $rate;
        $entity->setStatus(new Status(VehicleQuotaLimitStatusEnum::Status_Saved));

        return $entity;
    }

    /**
     * 设置状态
     * @param    Status $status
     */
    public function setStatus(Status $status)
    {
        $this->status = $status;
    }

    /**
     * 获取状态
     * @return   Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * 获取状态值
     * @return int
     */
    public function getStatusValue()
    {
        return $this->status->status;
    }

    /**
     * 参数校验
     * @return array
     */
    public function rules()
    {
        return [['rate', 'validateRate']];
    }

    /**
     * 验证当日额度占比
     * @param $attribute
     */
    public function validateRate($attribute)
    {
        $rate = $this->$attribute; //当前属性值
        if (empty($rate))
        {
            $this->addError($attribute, "当日额度占比不得为空");
        }

        if (round($rate * 100) <= 0 || round($rate * 100) > 100)
        {
            $this->addError($attribute, "当日额度占比范围为：0%~100%（不包含0）");
        }
    }
}