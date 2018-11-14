<?php
/**
 * This is Entity Repository Interface for RoleRight.
 * Auto Generated.
 * DateTime: 2018-08-24 16:17:41
 * Describe：
 *
 */

namespace app\ddd\Admin\Domain\Right;

use ddd\Common\Domain\IRepository;

/**
 * Interface IRoleRightRepository
 * @package ddd\RoleRight\Domain\Creditor
 *
 * @method RoleRight findById($id)
 * @method store($entity)
 */
interface IRoleRightRepository  extends IRepository
{
    /**
     * 根据id的组合字符串查询对象，格式："1,2,3"
     * @param $ids
     * @return RoleRight[]
     */
    public function findByIds($ids);
}