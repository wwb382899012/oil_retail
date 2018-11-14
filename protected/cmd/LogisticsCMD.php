<?php
use ddd\Infrastructure\DIService;
use ddd\Logistics\Domain\Driver\IDriverRepository;

/**
 * 物流企业外部接口
 * User: vector
 * Date: 2018/9/5
 * Time: 15:52
 */
class LogisticsCMD extends CMD
{
    public function __construct()
    {
        $this->actionMap = array(
            "91020001" => "addLogisticsCompany",
            #"91010002" => "addCompanyCreditQuota",
            "91020003" => "addDriver",
            "91020004" => "addVehicle",
            "91020005" => "bindVehicle",
            "91020006" => "getDriverInfo",
            "91020007" => "updateLogisticsStatus",
            "91020008" => "editDriver",
        );
    }

    /**
     * @api {POST} / [91020001]添加物流企业
     * @apiName 91020001
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {string} identity 银管家物流企业标识，<font color=red>必填</font>
     * @apiParam (输入字段) {string} name 企业名称，<font color=red>必填</font>
     * @apiParam (输入字段) {string} status 银管家状态，<font color=red>必填</font>
     * @apiParam (输入字段) {string} credit_quota 授信额度,单位分，<font color=red>必填</font>
     * @apiParam (输入字段) {string} start_date 额度有效开始日期(yyyy-mm-dd)，<font color=red>必填</font>
     * @apiParam (输入字段) {string} end_date 额度有效截止日期(yyyy-mm-dd)，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91020001",
     * "data":{
     *      "identity":"银管家物流企业标识",
     *      "name":"物流企业名字",
     *      "status":"银管家状态",
     *      "credit_quota":"授信额度",
     *      "start_date":"2018-09-12",
     *      "end_date":"2018-10-01"
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":{
     *          "logistics_id":"企业标识"
     *      }
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"error"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Logistics
     * @apiVersion 1.0.0
     */
    protected function addLogisticsCompany($params)
    {
        Utility::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 添加物流企业参数:' . json_encode($params),"info","retail.out");

        if(empty($params))
            return new \app\cmd\CMDResult(CMDCode::CODE_NO_POST_DATA);

        $dto = new \app\ddd\Logistics\DTO\LogisticsCompany\LogisticsCompanyDTO();
        $dto->out_identity = $params["identity"];
        $dto->name         = trim($params["name"]);
        $dto->out_status   = $params["status"];
        $dto->status       = \LogisticsCompany::EFFECTIVE_STATUS;
        $dto->credit_quota = trim($params["credit_quota"]);
        $dto->start_date   = $params["start_date"];
        $dto->end_date     = $params["end_date"];

       if(!$dto->validate())
        {
            Utility::log("添加物流企业接口参数错误，参数为：".json_encode($params)."，错误信息：".json_encode($dto->getErrors()),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_CHECK_ERROR, $dto->getErrors());
        }

        try{
            $logisticsService = new \app\ddd\Logistics\Application\LogisticsCompany\LogisticsCompanyService();
            $logisticsCompany = $logisticsService->addLogisticsCompany($dto->toEntity());
            $result           = $logisticsService->getLogisticsCompany($logisticsCompany->logistics_id);

            Utility::log("添加物流企业：".json_encode($result),"info","retail.out");

            $data["logistics_id"] = $result["logistics_id"];

            return \app\cmd\CMDResult::createSuccessResult($data);
        }
        catch (Exception $e)
        {
            Utility::log("添加物流企业出错，参数为：".json_encode($params)."，错误：".$e->getMessage(),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }


    /**
     * @api {POST} / [91020003]添加司机信息
     * @apiName 91020003
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {string} name 司机姓名，<font color=red>必填</font>
     * @apiParam (输入字段) {string} logistics_id 物流企业id，<font color=red>必填</font>
     * @apiParam (输入字段) {string} phone 手机号码，<font color=red>必填</font>
     * @apiParam (输入字段) {string} password 交易密码，<font color=red>必填</font>
     * @apiParam (输入字段) {string} status 状态，<font color=red>必填</font>
     * @apiParam (输入字段) {string} files 附件信息，<font color=red>必填</font>
     * @apiParam (输入字段) {string} files-file_id 附件id，<font color=red>必填</font>
     * @apiParam (输入字段) {string} files-file_url 附件地址，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91020003",
     * "data":{
     *      "name":"司机姓名",
     *      "logistics_id":"物流企业id",
     *      "phone":"手机号码",
     *      "password":"交易密码",
     *      "status":"状态",0-失效，1-有效
     *      "files":[
     *              {
     *              "file_id":"文件id",
     *              "file_url": "附件地址"
     *              }
     *      ]
     *     }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":{
     *          "customer_id":"客户id"
     *      }
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"error"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Logistics
     * @apiVersion 1.0.0
     */
    protected function addDriver($params)
    {
        Utility::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 添加司机接口参数:' . json_encode($params),"info","retail.out");

        if(empty($params))
            return new \app\cmd\CMDResult(CMDCode::CODE_NO_POST_DATA);

        $dto = new \app\ddd\Logistics\DTO\Driver\DriverDTO();
        $dto->logistics_id = $params["logistics_id"];
        $dto->name         = trim($params["name"]);
        $dto->phone        = trim($params["phone"]);
        $dto->status       = $params["status"];
        $dto->password     = trim($params["password"]);
        $dto->files        = $params["files"];

       if(!$dto->validate())
        {
            Utility::log("添加司机接口参数错误，参数为：".json_encode($params)."，错误信息：".json_encode($dto->getErrors()),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_CHECK_ERROR, $dto->getErrors());
        }

        try{
            $driver = \app\ddd\Logistics\Application\Driver\DriverService::service()->addDriver($dto->toEntity());

            $data['customer_id'] = $driver->customer_id;

            Utility::log("添加司机：".json_encode($data),"info","retail.out");

            return \app\cmd\CMDResult::createSuccessResult($data);
        }
        catch (Exception $e)
        {
            Utility::log("添加司机出错，参数为：".json_encode($params)."，错误：".$e->getMessage(),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }

    /**
     * @api {POST} / [91020004]添加车辆信息
     * @apiName 91020004
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {string} logistics_id 物流企业id，<font color=red>必填</font>
     * @apiParam (输入字段) {string} number 车牌号，<font color=red>必填</font>
     * @apiParam (输入字段) {string} model 车型，<font color=red>必填</font>
     * @apiParam (输入字段) {string} capacity 邮箱容量，单位升，保留两位小数，<font color=red>必填</font>
     * @apiParam (输入字段) {string} operator 操作人，<font color=red>必填</font>
     * @apiParam (输入字段) {string} start_date 行驶证开始日期，<font color=red>必填</font>
     * @apiParam (输入字段) {string} end_date 行驶证结束日期，<font color=red>必填</font>
     * @apiParam (输入字段) {string} files 附件信息，<font color=red>必填</font>
     * @apiParam (输入字段) {string} files-file_id 附件id，<font color=red>必填</font>
     * @apiParam (输入字段) {string} files-file_url 附件地址，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91020004",
     * "data":{
     *      "logistics_id":"物流企业id",
     *      "number":"车牌号",
     *      "model":"车型",
     *      "capacity":"邮箱容量",
     *      "operator":"操作人",
     *      "start_date":"2017-06-18",
     *      "end_date":"2018-07-18",
     *      "files":[
     *              {
     *              "file_id":"附件id",
     *              "file_url": "附件地址"
     *              }
     *      ]
     *     }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":{
     *          "vehicle_id":"车辆id"
     *      }
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"error"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Logistics
     * @apiVersion 1.0.0
     */
    protected function addVehicle($params)
    {
        Utility::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 添加车辆信息接口参数:' .json_encode($params),"info","retail.out");

        if(empty($params))
            return new \app\cmd\CMDResult(CMDCode::CODE_NO_POST_DATA);

        $dto = new \app\ddd\Logistics\DTO\Vehicle\VehicleDTO();
        $dto->logistics_id = $params["logistics_id"];
        $dto->number       = trim($params["number"]);
        $dto->model        = trim($params["model"]);
        $dto->capacity     = trim($params["capacity"]);
        $dto->operator     = trim($params["operator"]);
        $dto->start_date   = $params["start_date"];
        $dto->end_date     = $params["end_date"];
        $dto->status       = \Vehicle::PASS_STATUS;
        $dto->files        = $params["files"];


       if(!$dto->validate())
        {
            Utility::log("添加车辆接口参数错误，参数为：".json_encode($params)."，错误信息：".json_encode($dto->getErrors()),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_CHECK_ERROR, $dto->getErrors());
        }

        try{
            $vehicle  = \app\ddd\Logistics\Application\Vehicle\VehicleService::service()->addVehicle($dto->toEntity());

            $data['vehicle_id'] = $vehicle->getId();
            Utility::log("添加司机：".json_encode($data),"info","retail.out");
            return \app\cmd\CMDResult::createSuccessResult($data);
        }
        catch (Exception $e)
        {
            Utility::log("添加司机出错，参数为：".json_encode($params)."，错误：".$e->getMessage(),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }

    /**
     * @api {POST} / [91020005]绑定司机车辆信息
     * @apiName 91020005
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {string} customer_id 客户id，<font color=red>必填</font>
     * @apiParam (输入字段) {array}  vehicles 车辆信息，<font color=red>非必填</font>
     * @apiParam (输入字段) {string} vehicles-vehicle_id 车辆id，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91020005",
     * "data":{
     *      "customer_id":"客户id",
     *      "vehicles":[
     *              {
     *              "vehicle_id":"车辆id"
     *              }
     *          ]
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":"succ"
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"error"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Logistics
     * @apiVersion 1.0.0
     */
    protected function bindVehicle($params)
    {
        Utility::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 绑定车辆信息接口参数:' . json_encode($params),"info","retail.out");

        if(empty($params))
            return new \app\cmd\CMDResult(CMDCode::CODE_NO_POST_DATA);

        if(empty($params["customer_id"]) || empty($params["vehicles"]))
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_MISSING);

        try{

            \app\ddd\Logistics\Application\Driver\DriverService::service()->bindVehicle($params["customer_id"], $params["vehicles"]);

            return \app\cmd\CMDResult::createSuccessResult();
        }
        catch (Exception $e)
        {
            Utility::log("绑定车辆信息出错，参数为：".json_encode($params)."，错误：".$e->getMessage(),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }

    /**
     * @api {POST} / [91020006]获取司机车辆信息
     * @apiName 91020006
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {string} customer_id 客户id，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91020006",
     * "data":{
     *      "customer_id":"客户id"
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":{
     *          "customer_id":"客户id",
     *          "name":"司机姓名",
     *          "phone":"手机号码",
     *          "status":"状态", 0-失效，1-有效
     *          "vehicles": [
     *             {
     *                 "vehicle_id": "车辆标识",
     *                 "number": "车牌号",
     *                 "model":"车辆类型",
     *                 "capacity":"邮箱容量，单位升，保留两位小数",
     *                 "balance_capacity":"剩余油量",
     *                 "day_capacity":"当日油量",
     *                 "operator":"添加人员",
     *                 "add_time":"添加时间",
     *                 "status":"车辆状态", 2-审核通过
     *                 "status_name":"状态名",
     *                 "start_date": "开始日期",
     *                 "end_date": "结束日期"
     *             }
     *         ]
     *      }
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"error"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Logistics
     * @apiVersion 1.0.0
     */
    protected function getDriverInfo($params)
    {
        Utility::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 获取司机车辆信息接口参数:' . json_encode($params),"info","retail.out");

        if(empty($params["customer_id"]))
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_MISSING);

        try{
            $result = \app\ddd\Logistics\Application\Driver\DriverService::service()->getDriver($params["customer_id"]);

            Utility::log("获取司机车辆信息：".json_encode($result),"info","retail.out");
            return \app\cmd\CMDResult::createSuccessResult($result);
        }
        catch (Exception $e)
        {
            Utility::log("获取司机车辆信息出错，参数为：".json_encode($params)."，错误：".$e->getMessage(),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }


    /**
     * @api {POST} / [91020007]更新物流企业状态
     * @apiName 91020007
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {int} identity 银管家企业id，<font color=red>必填</font>
     * @apiParam (输入字段) {int} status 银管家状态，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91020007",
     * "data":{
     *      "identity":"银管家企业id",
     *      "status":"银管家状态" 
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
     * @apiGroup Out-Logistics
     * @apiVersion 1.0.0
     */
    protected function updateLogisticsStatus($params)
    {
        Utility::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 更新物流企业状态接口参数:' . json_encode($params),"info","retail.out");

        if(empty($params["identity"]) || !isset($params['status']))
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_MISSING);

        try{

            \app\ddd\Logistics\Application\LogisticsCompany\LogisticsCompanyService::service()->updateOutStatus($params["identity"], $params['status']);

            return \app\cmd\CMDResult::createSuccessResult();
        }
        catch (Exception $e)
        {
            Utility::log("更新物流企业状态出错，参数为：".json_encode($params)."，错误：".$e->getMessage(),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }

    /**
     * @api {POST} / [91020008]更新司机信息
     * @apiName 91020008
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {string} customer_id 客户id，<font color=red>必填</font>
     * @apiParam (输入字段) {string} password 交易密码，<font color=red>必填</font>
     * @apiParam (输入字段) {string} status 状态，<font color=red>必填</font>
     * @apiParam (输入字段) {string} files 附件信息，<font color=red>必填</font>
     * @apiParam (输入字段) {string} files-file_id 附件id，<font color=red>必填</font>
     * @apiParam (输入字段) {string} files-file_url 附件地址，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91020008",
     * "data":{
     *      "customer_id":"15",
     *      "password":"交易密码",
     *      "status":"状态",0-失效，1-有效
     *      "files":[
     *              {
     *              "file_id":"文件id",
     *              "file_url": "附件地址"
     *              }
     *      ]
     *     }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":{
     *          "customer_id":"客户id"
     *      }
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"error"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Logistics
     * @apiVersion 1.0.0
     */
    protected function editDriver($params)
    {
        Utility::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 修改司机接口参数:' . json_encode($params),"info","retail.out");

        if(empty($params))
            return new \app\cmd\CMDResult(CMDCode::CODE_NO_POST_DATA);

        $driver = DIService::getRepository(IDriverRepository::class)->findById($params['customer_id']);
        if(empty($driver))
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, '当前客户信息不存在');

        $dto = new \app\ddd\Logistics\DTO\Driver\DriverDTO();
        $dto->fromEntity($driver);

        $dto->status       = $params["status"];
        $dto->password     = trim($params["password"]);
        $dto->files        = $params["files"];

        if(!empty($dto->status)&&!is_numeric($dto->status))
        {
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_CHECK_ERROR, '状态status必须为数字');
        }
        if(!preg_match('/^\d{6}$/',$dto->password)){
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_CHECK_ERROR, '交易密码password必须是6位数字');
        }

        try{
            $driver = \app\ddd\Logistics\Application\Driver\DriverService::service()->editDriver($dto->toEntity());

            $data['customer_id'] = $driver->customer_id;

            Utility::log("添加司机：".json_encode($data),"info","retail.out");

            return \app\cmd\CMDResult::createSuccessResult($data);
        }
        catch (Exception $e)
        {
            Utility::log("添加司机出错，参数为：".json_encode($params)."，错误：".$e->getMessage(),"error","retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }
}
