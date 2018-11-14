<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/11 0011
 * Time: 9:40
 */

namespace ddd\Quota\DTO\LogisticsQuota;

use ddd\Common\Domain\BaseEntity;
use ddd\Quota\DTO\BaseRiskQuotaDTO;

class LogisticsQuotaDTO extends BaseRiskQuotaDTO
{
    /**
     * 物流企业id
     * @var   int
     */
    public $logistics_id = 0;

    /**
     * @param BaseEntity $entity
     */
    public function fromEntity(BaseEntity $entity)
    {
        parent::fromEntity($entity);
        $this->logistics_id = $entity->logistics_id;
    }
}