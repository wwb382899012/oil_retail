<?php

namespace ddd\OilStation\Domain;

use ddd\Common\Domain\IRepository;

interface IAreaDictTreeRepository extends IRepository{
    function getTree():AreaDictTree;
}