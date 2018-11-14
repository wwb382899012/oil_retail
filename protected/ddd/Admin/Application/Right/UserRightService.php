<?php
/**
 * User: liyu
 * Date: 2018/9/7
 * Time: 18:07
 * Desc: UserRightService.php
 */

namespace ddd\Admin\Application\Right;


use app\ddd\Admin\Domain\Right\UserRight;
use app\ddd\Admin\Domain\Right\UserRightRepository;
use app\ddd\Admin\Domain\User\SystemUserRepository;

use ddd\Common\Application\TransactionService;

class UserRightService extends TransactionService
{
    use UserRightRepository;
    use SystemUserRepository;

    /**
     * @desc 保存用户权限
     * @param UserRight $entity
     * @return bool|string
     */
    public function saveUserRight(UserRight $entity) {
        try {

            $this->beginTransaction();

            $this->getUserRightRepository()->store($entity);

            $userEntity = $this->getSystemUserRepository()->findById($entity->user_id);
            //修改用户的自动授权
            if ($userEntity->is_right_role == 1) {
                $userEntity->is_right_role = 0;
                $this->getSystemUserRepository()->store($userEntity);
            }

            $this->commitTransaction();
            return true;
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            return $e->getMessage();
        }
    }

}