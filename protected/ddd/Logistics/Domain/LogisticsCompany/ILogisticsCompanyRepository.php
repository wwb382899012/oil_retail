<?php
/**
 * Desc: 物流企业仓储接口
 * User: vector
 * Date: 2018/9/6
 * Time: 17:10
 */

namespace ddd\Logistics\Domain\LogisticsCompany;

use ddd\Common\Domain\IRepository;

interface ILogisticsCompanyRepository extends IRepository
{
	function getLogisticsIdByIdentity($outIdentity);
    function updateStatus(LogisticsCompany $entity);
    function clearCache($id=0);
    // function getAllLogisticsCompany();
	// function getActiveLogisticsCompany();
}