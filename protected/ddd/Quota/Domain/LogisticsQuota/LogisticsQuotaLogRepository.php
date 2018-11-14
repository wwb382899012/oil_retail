<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/4 0004
 * Time: 10:35
 */

namespace ddd\Quota\Domain\LogisticsQuota;


use ddd\Infrastructure\DIService;

trait LogisticsQuotaLogRepository
{
    /**
     * @var ILogisticsQuotaLogRepository
     */
    protected $logisticsQuotaLogRepository;

    /**
     * @desc 获取物流企业额度变更仓储
     * @return ILogisticsQuotaLogRepository
     * @throws \Exception
     */
    public function getLogisticsQuotaLogRepository()
    {
        if(empty($this->logisticsQuotaLogRepository)) {
            $this->logisticsQuotaLogRepository = DIService::getRepository(ILogisticsQuotaLogRepository::class);
        }

        return $this->logisticsQuotaLogRepository;
    }
}