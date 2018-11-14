<?php

namespace ddd\OilStation\Domain\OilStation;

use ddd\Common\Domain\IRepository;

interface IOilStationApplyRepository extends IRepository{
    function updateStatus(OilStationApply $entity):void;
}