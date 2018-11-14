<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/11 0011
 * Time: 9:40
 */

namespace ddd\Quota\DTO\VehicleQuota;

use ddd\Common\Domain\BaseEntity;
use ddd\Quota\DTO\BaseRiskQuotaDTO;

class VehicleDailyQuotaDTO extends BaseRiskQuotaDTO
{
    /**
     * 车辆id
     * @var   int
     */
    public $vehicle_id = 0;

    /**
     * 当前日期
     * @var date
     */
    public $current_date;

    /**
     * 转换为DTO对象
     * @name fromEntity
     * @param * @param BaseEntity $entity
     * @throw
     * @return void
     */
    public function fromEntity(BaseEntity $entity)
    {
        parent::fromEntity($entity);
        $this->vehicle_id = $entity->vehicle_id;
        $this->current_date = $entity->current_date;
    }
}