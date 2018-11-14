<?php

namespace ddd\OilStation\Domain\OilStation;

use ddd\Common\Domain\IRepository;

interface IOilStationRepository extends IRepository{
    function updateStatus(OilStation $entity):void;

    function getAllActiveStationIdNames():array ;

    function copyByApplyEntity(OilStationApply $entity):int;

    function getAllStationByCompanyId(int $companyId):array;
}