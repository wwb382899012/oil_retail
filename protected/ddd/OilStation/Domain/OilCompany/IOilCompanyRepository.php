<?php

namespace ddd\OilStation\Domain\OilCompany;

use ddd\Common\Domain\IRepository;

interface IOilCompanyRepository extends IRepository{
    function getAllActiveCompanyIdNames():array;

    function getAllCompanyIdNames():array;
}