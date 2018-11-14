<?php

namespace ddd\OilStation\Domain\OilGoods;

use ddd\Common\Domain\IRepository;

interface IOilGoodsRepository extends IRepository{
    //nobody

    function getAllActiveGoodsIdNames():array ;
}