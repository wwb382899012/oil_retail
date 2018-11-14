<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 10:13
 * Describe： 物流企业每日限额
 */

namespace app\ddd\Quota\DTO\LogisticsQuotaLimit;



use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;
use ddd\Quota\Domain\LogisticsQuotaLimit\LogisticsQuotaLimit;

class LogisticsQuotaLimitDTO extends BaseDTO
{
    #region property

    /**
     * 标识
     * @var      int
     */
    public $limit_id = 0;

    /**
     * 编号
     * @var      string
     */
    public $code;

    /**
     * 当日额度占比
     * @var      float
     */
    public $rate = 0;

    /**
     * 状态
     * @var      int
     */
    public $status;

    /**
     * 创建用户
     * @var      string
     */
    public $create_user_name;

    /**
     * 创建时间
     * @var      string
     */
    public $create_time;


    #endregion

    public function customAttributeNames()
    {
        return array();
    }

    public function rules()
    {
        return [
            ["rate","numerical", "allowEmpty" => false, "integerOnly" => true, "min" => 0,"max" => 100, "tooSmall" => "当日额度占比{attribute}范围为：0%~100%（不包含0）！","message"=>"当日额度占比{attribute}范围为：0%~100%（不包含0）！"],

        ];
    }

    /**
     * 转换为DTO对象
     * @name fromEntity
     * @param BaseEntity $entity
     * @throw
     * @return void
     */
    public function fromEntity(BaseEntity $entity)
    {
        $this->setAttributes($entity->getAttributes());
        if(!empty($entity->create_time))
            $this->create_time = $entity->create_time->toDateTime();
        $this->create_user_name = $entity->create_user->name;
    }

    /**
     * 转换为实对象
     * @name toEntity
     * @param
     * @throw
     * @return LogisticsQuotaLimit
     */
    public function toEntity()
    {
        $entity= new LogisticsQuotaLimit();
        $entity->setAttributes($this->getAttributes());
        $entity->create_time = new DateTime($this->create_time);
        return $entity;
    }

    /**
     * 对DTO进行赋值
     * @name assignDTO
     * @param * @param array $params
     * @throw
     * @return void
     */
    public function assignDTO(array $params){

        $this->setAttributes($params);

    }

}