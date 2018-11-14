<?php
/**
 * Desc: 客户仓储接口
 * User: vector
 * Date: 2018/9/10
 * Time: 17:10
 */

namespace ddd\Customer\Domain;

use ddd\Common\Domain\IRepository;

interface ICustomerRepository extends IRepository
{
    function getCustomerIdByPhone($phone);
    function getCustomerIdByOpenId($openId);
    function bindWeixin($customerId, $openId);
    // function getAllCustomer();
    // function getActiveCustomer();
    function updateStatus(Customer $entity);
    function clearCache($phone="", $openId="");
}