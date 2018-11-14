<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/3 0003
 * Time: 16:52
 */

namespace ddd\Quota\Repository;


use ddd\Common\Repository\EntityRepository;
use ddd\Quota\Domain\BaseEntity;
use ddd\Quota\Domain\IQuotaRepository;

abstract class RiskQuotaRepository extends EntityRepository implements IQuotaRepository
{
    /**
     * 获取redis键名
     * @return mixed
     */
    //abstract public function getDependencyKey();

    /**
     * 增加已使用额度
     * @param    BaseEntity $entity
     * @param    int $quota
     * @throws   \Exception
     */
    public function addUsedQuota($entity, $quota)
    {
        $res = $this->model()->updateByPk($entity->getId(), ['used_quota' => new \CDbExpression("used_quota+" . $quota)]);
        if ($res !== 1)
        {
            throw new \Exception("更新失败");
        }
    }

    /**
     * 减少已使用额度
     * @param    BaseEntity $entity
     * @param    int $quota
     * @throws   \Exception
     */
    public function subtractUsedQuota($entity, $quota)
    {
        $res = $this->model()->updateByPk($entity->getId(), ['used_quota' => new \CDbExpression("used_quota-" . $quota)]);
        if ($res !== 1)
        {
            throw new \Exception("更新失败");
        }
    }

    /**
     * 增加冻结额度
     * @param    BaseEntity $entity
     * @param    int $quota
     * @throws   \Exception
     */
    public function freezeQuota($entity, $quota)
    {
        $res = $this->model()->updateByPk($entity->getId(), ['frozen_quota' => new \CDbExpression("frozen_quota+" . $quota)]);
        if ($res !== 1)
        {
            throw new \Exception("更新失败");
        }
    }

    /**
     * 释放冻结额度
     * @param    BaseEntity $entity
     * @param    int $quota
     * @throws   \Exception
     */
    public function unfreezeQuota($entity, $quota)
    {
        $res = $this->model()->updateByPk($entity->getId(), ['frozen_quota' => new \CDbExpression("frozen_quota+" . $quota)]);
        if ($res !== 1)
        {
            throw new \Exception("更新失败");
        }
    }
}