<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/4 0004
 * Time: 10:35
 */

namespace ddd\Quota\Domain\LogisticsQuota;


use ddd\Infrastructure\DIService;

trait LogisticsDailyQuotaRepository
{
    /**
     * @var ILogisticsDailyQuotaRepository
     */
    protected $logisticsDailyQuotaRepository;

    /**
     * @desc 获取物流企业当日额度仓储
     * @return ILogisticsDailyQuotaRepository
     * @throws \Exception
     */
    public function getLogisticsDailyQuotaRepository()
    {
        if(empty($this->logisticsDailyQuotaRepository)) {
            $this->logisticsDailyQuotaRepository = DIService::getRepository(ILogisticsDailyQuotaRepository::class);
        }

        return $this->logisticsDailyQuotaRepository;
    }
}