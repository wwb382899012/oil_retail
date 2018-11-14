<?php

namespace ddd\OilStation\Domain\OilCompany;


use ddd\Common\BaseEnum;

class OilCompanyFileEnum extends BaseEnum{

    /**
     * 普通附件
     */
    const TYPE_DEFAULT = 1;

    /**
     * 证件附件
     */
    const TYPE_CERTIFICATE = 2;
}