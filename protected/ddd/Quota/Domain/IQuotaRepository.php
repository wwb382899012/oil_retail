<?php
/**
 * Desc: 额度仓储接口
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 14:34
 */
namespace ddd\Quota\Domain;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\IRepository;

interface IQuotaRepository extends IRepository
{
    /**
     * 增加已使用额度
     * @param    BaseEntity $entity
     * @param    int $quota
     */
    public function addUsedQuota($entity, $quota);

    /**
     * 减少已使用额度
     * @param    BaseEntity $entity
     * @param    int $quota
     */
    public function subtractUsedQuota($entity, $quota);

    /**
     * 增加冻结额度
     * @param    BaseEntity $entity
     * @param    int $quota
     */
    public function freezeQuota($entity, $quota);

    /**
     * 释放冻结额度
     * @param    BaseEntity $entity
     * @param    int $quota
     */
    public function unfreezeQuota($entity, $quota);
}