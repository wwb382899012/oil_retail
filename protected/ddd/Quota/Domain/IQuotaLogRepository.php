<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/13 0013
 * Time: 17:31
 */

namespace ddd\Quota\Domain;


use ddd\Common\Domain\IRepository;
use ddd\Common\IAggregateRoot;

interface IQuotaLogRepository extends IRepository
{
    /**
     * 获取额度变更记录对象id
     * @return mixed
     */
    public function getQuotaObjectId(IAggregateRoot $entity);
}