<?php
/**
 * This is Entity Repository Interface for SystemUser.
 * Auto Generated.
 * DateTime: 2018-08-29 11:29:03
 * Describe：
 *
 */

namespace app\ddd\Admin\Domain\User;

use ddd\Common\Domain\IRepository;

/**
 * Interface ISystemUserRepository
 *
 * @method SystemUser findById($id)
 * @method store($entity)
 */
interface ISystemUserRepository  extends IRepository
{
    public function saveMainRoleId(SystemUser $user);
    public function delUser($userId);
    function findByUserName($userName);
    function findAllUserIsRightRole($roleId);
}