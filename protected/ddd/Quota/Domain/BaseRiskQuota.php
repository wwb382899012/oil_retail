<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 16:16
 */

namespace ddd\Quota\Domain;


use ddd\Common\Domain\BaseEntity;

abstract class BaseRiskQuota extends BaseEntity implements IQuota
{

    /**
     * 标识
     * @var   int
     */
    public $id = 0;

    /**
     * 系统授信额度
     * @var   int
     */
    public $credit_quota = 0;

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
     * 可用额度
     * @var   float
     */
    protected $available_quota = 0;

    /**
     * 仓储
     * @var   IQuotaRepository
     */
    protected $repository;

    /**
     * 获取实际授信额度
     * @return   float
     */
    public function getActualCreditQuota()
    {
        return $this->credit_quota;
    }

    /**
     * 增加已使用额度
     * @param    float $quota
     */
    public function addUsedQuota($quota)
    {
        $this->repository->addUsedQuota($this, $quota);
        $this->used_quota += $quota;
    }

    /**
     * 减少已使用额度
     * @param    float $quota
     */
    public function subtractUsedQuota($quota)
    {
        $this->repository->subtractUsedQuota($this, $quota);
        $this->used_quota -= $quota;
    }

    /**
     * 增加冻结额度
     * @param    float $quota
     */
    public function freezeQuota($quota)
    {
        $this->repository->freezeQuota($this, $quota);
        $this->frozen_quota += $quota;
    }

    /**
     * 释放冻结额度
     * @param    float $quota
     */
    public function unfreezeQuota($quota)
    {
        $this->repository->unfreezeQuota($this, $quota);
        $this->frozen_quota -= $quota;
    }

    /**
     * 获取剩余可用额度
     * @return   float
     */
    public function getAvailableQuota()
    {
        return $this->getActualCreditQuota() - $this->getOccupiedQuota();
    }

    /**
     * 获取总占用额度
     * @return   float
     */
    public function getOccupiedQuota()
    {
        return $this->used_quota + $this->frozen_quota;
    }
}
