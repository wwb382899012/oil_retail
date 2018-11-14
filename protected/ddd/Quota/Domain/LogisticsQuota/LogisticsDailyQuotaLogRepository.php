<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/4 0004
 * Time: 10:35
 */

namespace ddd\Quota\Domain\LogisticsQuota;


use ddd\Infrastructure\DIService;

trait LogisticsDailyQuotaLogRepository
{
    /**
     * @var ILogisticsDailyQuotaLogRepository
     */
    protected $logisticsDailyQuotaLogRepository;

    /**
     * @desc 获取物流企业当日额度变更仓储
     * @return ILogisticsDailyQuotaLogRepository
     * @throws \Exception
     */
    public function getLogisticsDailyQuotaLogRepository()
    {
        if(empty($this->logisticsDailyQuotaLogRepository)) {
            $this->logisticsDailyQuotaLogRepository = DIService::getRepository(ILogisticsDailyQuotaLogRepository::class);
        }

        return $this->logisticsDailyQuotaLogRepository;
    }
}