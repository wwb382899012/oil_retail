<?php
/**
 * User: liyu
 * Date: 2018/9/13
 * Time: 16:15
 * Desc: OilPriceEnum.php
 */

namespace ddd\OilStation\Domain\OilPrice;


use ddd\Common\BaseEnum;

class OilPriceEnum extends BaseEnum{
    const STATUS_ACTIVE = 1;

    const STATUS_WAIT_ACTIVE = 0;

    const STATUS_INVALID = -1;
}