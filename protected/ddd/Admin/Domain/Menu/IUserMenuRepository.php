<?php

namespace app\ddd\Admin\Domain\Menu;


/**
 * Interface IUserMenuRepository
 */
interface IUserMenuRepository{
    /**
     * @param int $id
     * @return mixed
     */
    public function findById(int $id):UserMenu;
}