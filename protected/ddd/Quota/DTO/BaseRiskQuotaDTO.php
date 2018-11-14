<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/11 0011
 * Time: 11:47
 */

namespace ddd\Quota\DTO;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;

class BaseRiskQuotaDTO extends BaseDTO
{
    /**
     * 标识
     * @var   int
     */
    public $id = 0;

    /**
     * 当日总额度
     * @var   int
     */
    public $total_quota = 0;

    /**
     * 冻结额度
     * @var   float
     */
    public $frozen_quota = 0;

    /**
     * 已使用额度
     * @var   float
     */
    public $used_quota = 0;

    /**
     * 剩余可用额度
     * @var   float
     */
    public $available_quota = 0;

    /**
     * @param BaseEntity $entity
     */
    public function fromEntity(BaseEntity $entity)
    {
        $this->id = $entity->getId();
        $this->total_quota = $entity->getActualCreditQuota();
        $this->frozen_quota = $entity->frozen_quota;
        $this->used_quota = $entity->used_quota;
        $this->available_quota = $entity->getAvailableQuota();
    }
}