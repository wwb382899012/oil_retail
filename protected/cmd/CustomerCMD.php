<?php
/**
 * Desc:
 * User: vector
 * Date: 2018/9/6
 * Time: 11:38
 */

class CustomerCMD extends CMD
{
    public function __construct()
    {
        $this->actionMap = array(
            "91040001" => "checkUser",
            "91040002" => "getSMSCode",
            "91040003" => "smsLogin",
            "91040004" => "getCustomerId",
            "91040005" => "getCustomer",
            "91040006" => "updateCustomerStatus",
            "91040007" => "checkPassword",
        );
    }


    /**
     * @api {POST} / [91040001]检验客户
     * @apiName 91040001
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {int} customer_id 客户id，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91040001",
     * "data":{
     *      "customer_id":"客户id"
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":"success"
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"error"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Customer
     * @apiVersion 1.0.0
     */
    protected function checkUser($params)
    {
        Utility::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 客户检验参数:' . json_encode($params),"info","retail.out");

        if(empty($params["customer_id"]))
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_MISSING);

        try{

            \app\ddd\Customer\Application\CustomerService::service()->checkUser($params["customer_id"]);

            return \app\cmd\CMDResult::createSuccessResult();
        }
        catch (Exception $e)
        {
            Utility::log("客户检验出错，参数为：".json_encode($params)."，错误：".$e->getMessage(),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }

    /**
     * @api {POST} / [91040002]获取短信验证码
     * @apiName 91040002
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {int} phone 手机号，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91040002",
     * "data":{
     *      "phone":"手机号"
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回 ，注意，该接口为异步接口，只返回接收成功：
     * {
     * "code":0,
     * "data":"success"
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"error"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Customer
     * @apiVersion 1.0.0
     */
    protected function getSMSCode($params)
    {
        Utility::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 获取验证码参数:' . json_encode($params),"info","retail.out");

        if(empty($params["phone"]))
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_MISSING);

        try{
            $customerId = \ddd\Infrastructure\DIService::get(\app\ddd\Customer\Application\CustomerService::class)->getCustomerIdByPhone($params['phone']);

            if(!empty($customerId)) {
                \app\ddd\Customer\Application\CustomerService::service()->getSMSCode($params["phone"]);

                return \app\cmd\CMDResult::createSuccessResult();
            }
        }
        catch (Exception $e)
        {
            Utility::log("获取验证码出错，参数为：".json_encode($params)."，错误：".$e->getMessage(),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }

    /**
     * @api {POST} / [91040003]短信登录接口
     * @apiName 91040003
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {int} phone 手机号，<font color=red>必填</font>
     * @apiParam (输入字段) {int} code 短信验证码，<font color=red>必填</font>
     * @apiParam (输入字段) {int} open_id 微信openId，<font color=red>非必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91040003",
     * "data":{
     *      "phone":"手机号",
     *      "code":"短信验证码",
     *      "open_id":"微信openId"
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":{
     *      "customer_id":"客户id"
     *      }
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"error"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Customer
     * @apiVersion 1.0.0
     */
    protected function smsLogin($params)
    {
        Utility::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 短信登录接口参数:' . json_encode($params),"info","retail.out");

        if(empty($params))
            return new \app\cmd\CMDResult(CMDCode::CODE_NO_POST_DATA);

        if(empty($params["phone"]) || empty($params["code"]))
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_MISSING);


        $dto = new \app\ddd\Customer\DTO\CustomerDTO();
        $dto->phone   = trim($params["phone"]);
        $dto->account = trim($params["phone"]);
        $dto->code    = trim($params["code"]);
        $dto->open_id = $params["open_id"];

        try{

            $data['customer_id'] = \app\ddd\Customer\Application\CustomerService::service()->login($dto);

            Utility::log("短信登录接口返回结果：".json_encode($data), "info", "retail.out");
            return \app\cmd\CMDResult::createSuccessResult($data);
        }
        catch (Exception $e)
        {
            Utility::log("短信登录接口出错，参数为：".json_encode($params)."，错误：".$e->getMessage(),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }

    /**
     * @api {POST} / [91040004]获取客户id
     * @apiName 91040004
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {int} open_id 微信openId，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91040004",
     * "data":{
     *      "open_id":"微信openId"
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":{
     *      "customer_id":"客户id"
     *      }
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"error"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Customer
     * @apiVersion 1.0.0
     */
    protected function getCustomerId($params)
    {
        Utility::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 获取客户id接口参数:' . json_encode($params),"info","retail.out");

        if(empty($params["open_id"]))
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_MISSING);

        try{

            $data['customer_id'] = \app\ddd\Customer\Application\CustomerService::service()->getCustomerId($params["open_id"]);

            return \app\cmd\CMDResult::createSuccessResult($data);
        }
        catch (Exception $e)
        {
            Utility::log("获取客户id出错，参数为：".json_encode($params)."，错误：".$e->getMessage(),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    
    }

    /**
     * @api {POST} / [91040005]获取客户信息
     * @apiName 91040005
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {int} customer_id 客户id，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91040005",
     * "data":{
     *      "customer_id":"客户id"
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":{
     *      "customer":{
     *                 "customer_id": "客户id",
     *                 "name": "姓名",
     *                 "phone":"手机号",
     *                 "status":"客户状态"
     *                },
     *       "company":{
     *                 "logistics_id": "物流企业id",
     *                 "name": "企业名称",
     *                 "status":"企业状态", //1-正常，0-无效
     *                }
     *      }
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"error"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Customer
     * @apiVersion 1.0.0
     */
    protected function getCustomer($params)
    {
        Utility::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 获取客户信息接口参数:' . json_encode($params),"info","retail.out");

        if(empty($params["customer_id"]))
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_MISSING);

        try{

            $result = \app\ddd\Customer\Application\CustomerService::service()->getCustomer($params["customer_id"]);

            Utility::log("获取客户信息：".json_encode($result),"info","retail.out");

            return \app\cmd\CMDResult::createSuccessResult($result);
        }
        catch (Exception $e)
        {
            Utility::log("获取客户信息出错，参数为：".json_encode($params)."，错误：".$e->getMessage(),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }


    /**
     * @api {POST} / [91040006]更新客户状态
     * @apiName 91040006
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {int} customer_id 客户id，<font color=red>必填</font>
     * @apiParam (输入字段) {int} status 客户状态，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91040006",
     * "data":{
     *      "customer_id":"客户id",
     *      "status":"客户状态"   // 0-无效，1-有效
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":"success"
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"error"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Customer
     * @apiVersion 1.0.0
     */
    protected function updateCustomerStatus($params)
    {
        Utility::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 更新客户状态接口参数:' . json_encode($params),"info","retail.out");

        if(empty($params["customer_id"]) || !isset($params['status']))
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_MISSING);

        try{

            \app\ddd\Customer\Application\CustomerService::service()->updateStatus($params["customer_id"], $params['status']);

            return \app\cmd\CMDResult::createSuccessResult();
        }
        catch (Exception $e)
        {
            Utility::log("更新客户状态接口出错，参数为：".json_encode($params)."，错误：".$e->getMessage(),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }


    /**
     * @api {POST} / [91040007]检验客户交易密码
     * @apiName 91040007
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {int} customer_id 客户id，<font color=red>必填</font>
     * @apiParam (输入字段) {int} password 客户交易密码，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91040007",
     * "data":{
     *      "customer_id":"客户id",
     *      "password":"客户交易密码"
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":"success"
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"error"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Customer
     * @apiVersion 1.0.0
     */
    protected function checkPassword($params)
    {
        Utility::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 客户交易密码检验参数:' . json_encode($params),"info","retail.out");

        if(empty($params["customer_id"]) || empty($params['password']))
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_MISSING);

        try{

            \app\ddd\Customer\Application\CustomerService::service()->checkPassword($params["customer_id"], $params["password"]);

            return \app\cmd\CMDResult::createSuccessResult();
        }
        catch (Exception $e)
        {
            Utility::log("客户交易密码检验出错，参数为：".json_encode($params)."，错误：".$e->getMessage(),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }

}