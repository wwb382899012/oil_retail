<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/4 0004
 * Time: 10:35
 */

namespace ddd\Quota\Domain\LogisticsQuota;


use ddd\Infrastructure\DIService;

trait LogisticsQuotaRepository
{
    /**
     * @var ILogisticsQuotaRepository
     */
    protected $logisticsQuotaRepository;

    /**
     * @desc 获取物流企业额度仓储
     * @return ILogisticsQuotaRepository
     * @throws \Exception
     */
    public function getLogisticsQuotaRepository()
    {
        if(empty($this->logisticsQuotaRepository)) {
            $this->logisticsQuotaRepository = DIService::getRepository(ILogisticsQuotaRepository::class);
        }

        return $this->logisticsQuotaRepository;
    }
}