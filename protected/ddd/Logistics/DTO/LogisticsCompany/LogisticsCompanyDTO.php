<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 10:13
 * Describe： 物流企业
 */

namespace app\ddd\Logistics\DTO\LogisticsCompany;



use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\Logistics\Domain\LogisticsCompany\LogisticsCompany;
use ddd\Logistics\Domain\LogisticsCompany\LogisticsCompanyCreditQuota;


class LogisticsCompanyDTO extends BaseDTO
{
    #region property

    /**
     * 标识(编号)
     * @var      int
     */
    public $logistics_id = 0;

    /**
     * 企业名称
     * @var      string
     */
    public $name;

    /**
     * 银管家标识
     * @var      string
     */
    public $out_identity;

    /**
     * 银管家状态值
     * @var      int
     */
    public $out_status;

    /**
     * 银管家状态
     * @var      string
     */
    public $out_status_name;

    /**
     * 企业状态值
     * @var      int
     */
    public $status;

    /**
     * 企业状态
     * @var      string
     */
    public $status_name;

    /**
     * 授信额度
     * @var int
     */
    public $credit_quota;

    /**
     * 授信额度开始日期
     * @var string
     */
    public $start_date;

    /**
     * 结束日期
     * @var string
     */
    public $end_date;


    #endregion

    public function rules()
    {
        return [
            ["out_identity,name,start_date","required"],
            ["out_status","numerical", "allowEmpty" => false, "integerOnly" => true, "min" => 0, "tooSmall" => "银管家状态{attribute}必须为数字", 'on'=>'auto'],
            ["credit_quota","numerical", "allowEmpty" => false, "integerOnly" => true, "min" => 0, "tooSmall" => "金额{attribute}必须为非负数字"],
            ["end_date","validateDate",],
        ];
    }

    /**
     * 校验日期
     * @param $attribute
     * @param $params
     */
    public function validateDate($attribute, $params)
    {
        if(empty($this->$attribute))
            $this->addError($attribute,"".$attribute."不得为空");

        $startTime = strtotime($this->start_date);
        $endTime   = strtotime($this->end_date);
        // $nowTime   = strtotime(date('Y-m-d'));
        if($endTime < $startTime)
            $this->addError($attribute, '截止日期不得早于开始日期');
        // else if ($endTime < $nowTime)
        //     $this->addError($attribute, '截止日期不得早于当前日期');
        // else if ($nowTime < $startTime)
        //     $this->addError($attribute, '开始日期不得早于当前日期');
    }

    /**
     * 转换为DTO对象
     * @name fromEntity
     * @param * @param BaseEntity $entity
     * @throw
     * @return void
     */
    public function fromEntity(BaseEntity $entity)
    {
        $values = $entity->getAttributes(["logistics_id","name","out_identity"]);
        $this->setAttributes($values);
        $this->status          = $entity->getStatus()->status;
        $this->status_name     = \Map::$v['logistics_company_status'][$this->status];
        $this->out_status      = $entity->getOutStatus()->status;
        $this->out_status_name =  \Map::$v['logistics_company_out_status'][$this->out_status];
        $this->credit_quota    = $entity->credit_quota->credit_quota->amount;
        $this->start_date      = $entity->credit_quota->start_date->toDate();
        $this->end_date        = $entity->credit_quota->end_date->toDate();
    }

    /**
     * 转换为实体对象
     * @name toEntity
     * @param
     * @throw * @throws \ddd\Logistics\Domain\LogisticsCompany\ZException
     * @return LogisticsCompany
     */
    public function toEntity()
    {
        $entity= new LogisticsCompany();
        $entity->setAttributes($this->getAttributes(["logistics_id","name","out_identity"]));
        $entity->setStatus(new Status($this->status));
        $entity->setOutStatus(new Status($this->out_status));

        $LogisticsCompanyCreditQuota = new LogisticsCompanyCreditQuota();
        $LogisticsCompanyCreditQuota = $LogisticsCompanyCreditQuota::create($this->credit_quota, $this->start_date, $this->end_date);
        $entity->addCreditorQuota($LogisticsCompanyCreditQuota);
        
        return $entity;
    }

    /**
     * 对DTO赋值
     * @name assignDTO
     * @param * @param array $params
     * @throw
     * @return void
     */
    public function assignDTO(array $params){

        $this->setAttributes($params);
    }

}