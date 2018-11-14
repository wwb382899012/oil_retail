<?php
/**
 * This is Entity Repository Interface for SystemRole.
 * Auto Generated.
 * DateTime: 2018-08-29 11:32:19
 * Describe：
 *
 */

namespace app\ddd\Admin\Domain\Role;

use ddd\Common\Domain\IRepository;

/**
 * Interface ISystemRoleRepository
 *
 * @method SystemRole findById($id)
 * @method store($entity)
 */
interface ISystemRoleRepository  extends IRepository
{
    function findAll($condition = '', $params = array());

    function delRole($roleId);

    function findByName($name);
}