<?php

namespace ddd\OilStation\Domain\OilPrice;


use ddd\Common\BaseEnum;

class OilPriceApplyEnum  extends BaseEnum{

    const STATUS_SAVED = 0;
    const STATUS_BACKED = 3;
    const STATUS_SUBMIT = 5;
    const STATUS_PASSED = 7;
}