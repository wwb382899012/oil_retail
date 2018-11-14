<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 16:32
 */

namespace ddd\Quota\Domain;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\DateTime;

abstract class BaseRiskQuotaLog extends BaseEntity
{

    /**
     * 标识
     * @var   int
     */
    public $id = 0;

    /**
     * 1:下单 2:物流还款
     * 变更类型
     * @var   int
     */
    public $category = 0;

    /**
     * 1:增加  -1:减少
     * 增减方式
     * @var   int
     */
    public $method = 0;

    /**
     * 导致变更源对象id
     * @var   int
     */
    public $relation_id = 0;

    /**
     * 变更额度
     * @var   int
     */
    public $quota = 0;

    /**
     * 当前总额度
     * @var   int
     */
    public $quota_total = 0;

    /**
     * 备注
     * @var   string
     */
    public $remark;

    /**
     * 时间
     * @var   DateTime
     */
    public $create_time;

    /**
     * 设置增减方式
     * @param    int $quota
     */
    public function initMethod($quota = null)
    {
        $quota = $quota !== null ? $quota : $this->quota;
        if ($quota >= 0)
        {
            $this->method = AddSubtractEnum::ADD;
        } else
        {
            $this->method = AddSubtractEnum::SUBTRACT;
        }
    }

    /**
     * 获取额度变更对象属性名
     * @return mixed
     */
    abstract public function getQuotaObjectPropertyName();
}
