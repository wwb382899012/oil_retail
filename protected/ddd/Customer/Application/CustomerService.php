<?php
/**
 * Created by vector.
 * DateTime: 2018/9/11 10:38
 * Describe：客户
 */

namespace app\ddd\Customer\Application;


use app\ddd\Common\Domain\Value\Status;
use app\ddd\Customer\DTO\CustomerDTO;
use ddd\Common\Application\BaseService;
use ddd\Common\Application\Transaction;
use ddd\Customer\Domain\ICustomerRepository;
use ddd\Infrastructure\DIService;
use ddd\Infrastructure\Utility;
use ddd\Infrastructure\error\ZEntityNotExistsException;
use ddd\Infrastructure\error\ZException;
use ddd\Logistics\Domain\Driver\IDriverRepository;
use ddd\Logistics\Domain\LogisticsCompany\ILogisticsCompanyRepository;

class CustomerService extends BaseService
{
    use Transaction;

    /**
     * [checkUser 检验客户]
     * @param
     * @param  [bigint] $id [客户id]
     * @return [boolean]
     */
    public function checkUser($id)
    {
        if(empty($id))
            throw new ZException("参数有误");
        $customer = DIService::getRepository(ICustomerRepository::class)->findById($id);

        if(empty($customer))
            throw new ZException("当前客户不存在");

        return true;
    }

    /**
     * [getSMSCode 获取验证码]
     * @param
     * @param  [string] $phone [手机号]
     * @return [string]
     */
    public function getSMSCode($phone)
    {
        if(empty($phone))
            throw new ZException("参数有误");

        $phone = trim($phone);
        if(!preg_match("/^1[345678]{1}\d{9}$/",$phone))
            throw new ZException("手机号码不正确");

        $customerId = DIService::getRepository(ICustomerRepository::class)->getCustomerIdByPhone($phone);
        if(empty($customerId))
            throw new ZException("当前客户不存在");

        $bool = \SmsService::isCanSendSms($phone);
        if(!$bool)
            throw new ZException("手机获取验证码次数超限，请明天再试");
        
        \AMQPService::publishSendCode($phone);
    }


    /**
     * 获取客户信息
     * @param $id
     * @return int
     */
    public function getCustomer($id)
    {
        if(empty($id))
            throw new ZException("参数有误");

        $customer = DIService::getRepository(ICustomerRepository::class)->findById($id);
        if(empty($customer))
            throw new ZException("当前客户不存在"); 

        $driver = DIService::getRepository(IDriverRepository::class)->findById($id);
        if(empty($driver))
            throw new ZException("当前客户不存在"); 

        $logisticsCompany = DIService::getRepository(ILogisticsCompanyRepository::class)->findById($driver->company->id);
        if(empty($logisticsCompany))
            throw new ZException("当前物流企业不存在"); 

        $result['customer']['customer_id'] = $id;
        $result['customer']['name']        = $driver->name;
        $result['customer']['phone']       = $customer->phone;
        $result['customer']['status']      = $customer->getStatus()->status;
        $result['company']['logistics_id'] = $logisticsCompany->getId();
        $result['company']['name']         = $logisticsCompany->name;
        $result['company']['status']       = (int)$logisticsCompany->isActive();

        return $result;
    }


    public function login(CustomerDTO $dto)
    {
        if(empty($dto))
            throw new ZException("参数有误");

        try
        {
            $codeKey = \SmsService::$cacheKeyPrefix.\SmsService::$cacheCode.$dto->phone;
            $code    = \Utility::getCache($codeKey);
            $numKey  = \SmsService::$cacheKeyPrefix.\SmsService::$cacheNum.$dto->phone;
            $num     = \Utility::getCache($numKey);

            if($num>0 && empty($code))
                throw new ZException("验证码失效");

            if($dto->code != $code)
                throw new ZException("验证码错误");

            $customerId = DIService::getRepository(ICustomerRepository::class)->getCustomerIdByPhone($dto->phone);

            if(empty($customerId))
                throw new ZException("当前客户不存在");

            if(!empty($dto->open_id)){
                try{

                    $this->beginTransaction();

                    DIService::getRepository(ICustomerRepository::class)->bindWeixin($customerId, $dto->open_id);

                    $this->commitTransaction();

                }catch (\Exception $e)
                {
                    $this->rollbackTransaction();
                    throw new ZException($e->getMessage(),$e->getCode());
                }
                
            }

            return $customerId;
        }
        catch (\Exception $e)
        {
            throw new ZException($e->getMessage(),$e->getCode());
        }
    }


    public function getCustomerId($openId)
    {
        if(empty($openId))
            throw new ZException("参数有误");

        $customerId = DIService::getRepository(ICustomerRepository::class)->getCustomerIdByOpenId($openId);
        if(empty($customerId))
            throw new ZException("当前客户不存在"); 

        return $customerId;
    }


    public function updateStatus($customerId, $status)
    {
        if(empty($customerId) || !isset($status))
            throw new ZException("参数有误");

        $customer = DIService::getRepository(ICustomerRepository::class)->findById($customerId);
        if(empty($customer))
            throw new ZException("当前客户不存在");

        $driver = DIService::getRepository(IDriverRepository::class)->findById($customerId);
        if(empty($driver))
            throw new ZException("当前客户不存在"); 

        $nowTime = Utility::getNow();

        $customer->setStatus(new Status($status,$nowTime));
        $driver->setStatus(new Status($status, $nowTime));

        try
        {
            $this->beginTransaction();

            DIService::getRepository(ICustomerRepository::class)->updateStatus($customer);
            DIService::getRepository(IDriverRepository::class)->updateStatus($driver);
            
            $this->commitTransaction();

            return true;
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw new ZException($e->getMessage(),$e->getCode());
        }
    }

    /**
     * [checkPassword 交易客户交易密码]
     * @param
     * @param  [int] $customerId [客户id]
     * @param  [string] $password   [交易密码]
     * @return [bool]
     */
    public function checkPassword($customerId, $password)
    {
        if(empty($customerId) || empty($password))
            throw new ZException("参数有误");

        $password = trim($password);

        if(!preg_match('/^\d{6}$/', $password))
            throw new ZException("支付密码必须是6位数字");

        $driver = DIService::getRepository(IDriverRepository::class)->findById($customerId);
        if(empty($driver))
            throw new ZException("当前客户不存在"); 

        if($driver->password != $password)
            throw new ZException("支付密码不正确");

        return true;
    }

    /**
     * 根据手机号获取客户id
     * @param $phone
     * @return mixed
     * @throws ZException
     */
    public function getCustomerIdByPhone($phone)
    {
        if(empty($phone))
            throw new ZException("参数有误");

        $customerId = DIService::getRepository(ICustomerRepository::class)->getCustomerIdByPhone($phone);
        if(empty($customerId))
            throw new ZException("当前客户不存在");

        return $customerId;
    }
}