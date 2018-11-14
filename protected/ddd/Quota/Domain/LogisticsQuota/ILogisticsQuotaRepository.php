<?php
/**
 * Desc: 物流企业额度仓储接口
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 14:41
 */
namespace ddd\Quota\Domain\LogisticsQuota;

use ddd\Quota\Domain\IQuotaRepository;

interface ILogisticsQuotaRepository extends IQuotaRepository
{

    /**
     * 根据物流企业id获取额度信息
     * @param    int $logistics_id
     * @return   LogisticsQuota
     */
    public function findByLogisticsId($logistics_id);
}