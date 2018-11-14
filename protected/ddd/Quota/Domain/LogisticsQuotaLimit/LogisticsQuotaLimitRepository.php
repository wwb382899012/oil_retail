<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/5 0005
 * Time: 10:27
 */

namespace ddd\Quota\Domain\LogisticsQuotaLimit;


use ddd\Infrastructure\DIService;

trait LogisticsQuotaLimitRepository
{
    /**
     * @var ILogisticsQuotaLimitRepository
     */
    protected $logisticsQuotaLimitRepository;

    /**
     * @desc 获取仓储
     * @return ILogisticsQuotaLimitRepository
     * @throws \Exception
     */
    protected function getLogisticsQuotaLimitRepository()
    {
        if (empty($this->logisticsQuotaLimitRepository))
        {
            $this->logisticsQuotaLimitRepository = DIService::getRepository(ILogisticsQuotaLimitRepository::class);
        }

        return $this->logisticsQuotaLimitRepository;
    }
}