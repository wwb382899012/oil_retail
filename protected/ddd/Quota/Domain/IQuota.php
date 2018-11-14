<?php
/**
 * Desc: 额度接口
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 16:13
 */

namespace ddd\Quota\Domain;


interface IQuota
{

    /**
     * 获取实际授信额度
     * @return   float
     */
    public function getActualCreditQuota();

    /**
     * 增加已使用额度
     * @param    float $quota
     */
    public function addUsedQuota($quota);

    /**
     * 减少已使用额度
     * @param    float $quota
     */
    public function subtractUsedQuota($quota);

    /**
     * 增加冻结额度
     * @param    float $quota
     */
    public function freezeQuota($quota);

    /**
     * 释放冻结额度
     * @param    float $quota
     */
    public function unfreezeQuota($quota);

    /**
     * 获取剩余可用额度
     * @return   float
     */
    public function getAvailableQuota();

    /**
     * 获取总占用额度
     * @return   float
     */
    public function getOccupiedQuota();
}