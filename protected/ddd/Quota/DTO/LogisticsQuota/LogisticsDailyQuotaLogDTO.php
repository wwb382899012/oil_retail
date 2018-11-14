<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/28 10:13
 * Describe：每日额度日志
 */

namespace app\ddd\Qutoa\DTO\LogisticsQuota;


use app\ddd\Common\Domain\Value\Status;
use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\Quota\Domain\LogisticsQuota\LogisticsDailyQuotaLog;

class LogisticsDailyQuotaLogDTO extends BaseDTO
{
    #region property

    /**
     * 标识
     * @var      int
     */
    public $limit_id = 0;

    /**
     * 时间
     * @var      string
     */
    public $addDate;

    /**
     * 额度明细（元）
     * @var      float
     */
    public $quota = 0;

    /**
     * 编号
     * @var      Status
     */
    protected $code;

    /**
     * 收支类型
     * @var      int
     */
    public $category;


    #endregion

    public function customAttributeNames()
    {
        return array();
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
        $this->setAttributes($entity->getAttributes());

    }

    /**
     * 转换为实体对象
     * @name toEntity
     * @param
     * @throw
     * @return LogisticsDailyQuotaLog
     */
    public function toEntity()
    {
        $entity= new LogisticsDailyQuotaLog();
        $entity->setAttributes($this->getAttributes());
        return $entity;
    }

}