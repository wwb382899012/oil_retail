<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/9/5 0005
 * Time: 17:26
 */

class OrderCMD extends CMD
{
    public function __construct()
    {
        $this->actionMap = array(
            "91010001" => "doOrder",
            "91010002" => "getOrderDetail",
            "91010003" => "getCustomerOrders",
            "91010004" => "getDoOrderVehicles"
        );
    }

    /**
     * 获取订单应用层服务
     * @return \ddd\Order\Application\Order\OrderOutService|object
     * @throws Exception
     */
    private function getOrderOutService()
    {
        return \ddd\Infrastructure\DIService::get(\ddd\Order\Application\OrderOutService::class);
    }

    /**
     * @api {POST} / [91010001]下单
     * @apiName 91010001
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {int} customer_id 司机id，<font color=red>必填</font>
     * @apiParam (输入字段) {int} vehicle_id 车辆id，<font color=red>必填</font>
     * @apiParam (输入字段) {int} station_id 油站id，<font color=red>必填</font>
     * @apiParam (输入字段) {int} goods_id 油品id，<font color=red>必填</font>
     * @apiParam (输入字段) {float} quantity 升数，保留两位小数，<font color=red>必填</font>
     * @apiParam (输入字段) {string} password 交易密码，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91010001"
     * "data":{
     *      "customer_id":"司机id",
     *      "vehicle_id":"车辆id",
     *      "station_id":"油站id",
     *      "goods_id":"油品id",
     *      "quantity":"升数",
     *      "password":"交易密码",
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":{
     *      "order_id": "订单id",
     *      "status": "订单状态 -1:订单失败 10:订单成功",
     *      "failed_reason": "失败原因"
     * }
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"本次加油量超过汽车油箱的最大容量"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示接口调用成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Order
     * @apiVersion 1.0.0
     */
    protected function doOrder($params)
    {
        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 下单入参:' . json_encode($params), "info", "retail.out");

        $requiredParams = ['customer_id', 'vehicle_id', 'station_id', 'goods_id', 'quantity', 'password'];
        $checkRes = $this->checkArrayParams($params, $requiredParams);

        if ($checkRes !== true)
        {
            return $checkRes;
        }

        try
        {
            $order = $this->getOrderOutService()->doOrder($params['customer_id'], $params['vehicle_id'], $params['station_id'], $params['goods_id'], $params['quantity'], $params['password']);

            $result['order_id'] = $order->getId();
            $result['failed_reason'] = $order->failed_reason;
            $result['status'] = $order->getStatusValue();

            return new \app\cmd\CMDResult(0, $result);
        } catch (Exception $e)
        {
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 下单失败:' . $e->getMessage(), "error", "retail.out");

            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }

    /**
     * @api {POST} / [91010002]获取订单详情
     * @apiName 91010002
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {int} order_id 订单id，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91010002"
     * "data":{
     *      "order_id": "订单id",
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":{
     *      "order_id": "订单id",
     *      "code": "订单编号",
     *      "status": "订单状态",
     *      "status_desc": "订单状态描述",
     *      "quantity": "升数",
     *      "sell_amount": "油品总销售价，单位：分",
     *      "buy_amount": "油品总采购价，单位：分",
     *      "retail_price": "零售价，单位：分",
     *      "agreed_price": "协议价，单位：分",
     *      "discount_price": "优惠价，单位：分",
     *      "create_time": "订单提交时间，格式：yyyy-mm-dd HH:MM:SS",
     *      "effect_time": "订单生效时间，格式：yyyy-mm-dd HH:MM:SS",
     *      "failed_reason": "失败原因",
     *      "remark":"备注",
     *      "order_type":"订单类型",
     *      "goods": {
     *          "id": "油品id",
     *          "name": "油品名",
     *      }
     *      "customer": {
     *          "id": "司机id",
     *          "name": "司机名称",
     *          "phone": "手机号码",
     *      },
     *      "logistics" {
     *          "id": "物流公司id",
     *          "name": "物流公司名称"
     *      }
     *      "vehicle": {
     *          "id": "车辆id",
     *          "number": "车牌号",
     *          "model": "车型",
     *      }
     *      "oil_station": {
     *          "id": "油站id",
     *          "name": "油站名",
     *          "address": "油站地址",
     *      }
     * }
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"failed"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Order
     * @apiVersion 1.0.0
     */
    protected function getOrderDetail($params)
    {
        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 获取订单详情入参:' . json_encode($params), "info", "retail.out");
        $requiredParams = ['order_id'];
        $checkRes = $this->checkArrayParams($params, $requiredParams);

        if ($checkRes !== true)
        {
            return $checkRes;
        }

        try
        {
            $orderDetail = $this->getOrderOutService()->getOrderDetail($params['order_id']);
            return new \app\cmd\CMDResult(0, $orderDetail);
        } catch (Exception $e)
        {
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 获取订单详情失败:' . $e->getMessage(), "error", "retail.out");

            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }

    /**
     * @api {POST} / [91010003]获取用户订单列表
     * @apiName 91010003
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {int} customer_id 用户id，<font color=red>必填</font>
     * @apiParam (输入字段) {int} page 当前页码，<font color=red>非必填，默认为1</font>
     * @apiParam (输入字段) {int} pageSize 分页大小，<font color=red>非必填，默认为20</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91010003"
     * "data":{
     *      "customer_id": "用户id",
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":{
     *      "pageCount": 1,
     *      "total": 10,
     *      "page": 1,
     *      "rows": [{
     *          "order_id": "订单id",
     *          "code": "订单编号",
     *          "status": "订单状态",
     *          "status_desc": "订单状态描述",
     *          "quantity": "升数",
     *          "sell_amount": "油品总销售价，单位：分",
     *          "buy_amount": "油品总采购价，单位：分",
     *          "retail_price": "零售价，单位：分",
     *          "agreed_price": "协议价，单位：分",
     *          "discount_price": "优惠价，单位：分",
     *          "create_time": "订单提交时间，格式：yyyy-mm-dd HH:MM:SS",
     *          "effect_time": "订单生效时间，格式：yyyy-mm-dd HH:MM:SS",
     *          "failed_reason": "失败原因",
     *          "goods_id": "油品id",
     *          "goods_name": "油品名",
     *          "customer_id": "司机id",
     *          "customer_name": "司机名称",
     *          "customer_phone": "手机号码",
     *          "logistics_id": "物流公司id",
     *          "logistics_name": "物流公司名称"
     *          "vehicle_id": "车辆id",
     *          "vehicle_number": "车牌号",
     *          "vehicle_model": "车型",
     *          "oil_station_id": "油站id",
     *          "oil_station_name": "油站名",
     *          "oil_station_address": "油站地址"
     *          }]
     * }
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"failed"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Order
     * @apiVersion 1.0.0
     */
    protected function getCustomerOrders($params)
    {
        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 获取客户订单列表入参:' . json_encode($params), "info", "retail.out");
        $requiredParams = ['customer_id'];
        $checkRes = $this->checkArrayParams($params, $requiredParams);

        if ($checkRes !== true)
        {
            return $checkRes;
        }

        try
        {
            $customer = \ddd\Infrastructure\DIService::get(\app\ddd\Logistics\Application\Driver\DriverService::class)->getDriver($params['customer_id']);
            if (empty($customer))
            {
                \ddd\Infrastructure\error\ExceptionService::throwBusinessException(\ddd\Infrastructure\error\BusinessError::Customer_Not_Exist, ['customer_id' => $params['customer_id']]);
            }

            $paginationParams = [
                'order' => 'create_time desc',
                'condition' => 'customer_id=:customerId',
                'params' => ['customerId' => $params['customer_id']],
            ];
            $data = Order::model()->findAllByPageToArray($paginationParams, 0, 20);
            if (Utility::isNotEmpty($data['rows']))
            {
                foreach ($data['rows'] as $index => $row)
                {
                    $data['rows'][$index]['status_desc'] = Map::getStatusName("order_status", $row['status']);

                    $goods = OilGoods::model()->findByPk($row['goods_id']);
                    $data['rows'][$index]['goods_name'] = !empty($goods) ? $goods->name : '';

                    $customer = Driver::model()->find('customer_id='.$row['customer_id']);
                    $data['rows'][$index]['customer_name'] = !empty($customer) ? $customer->name : '';
                    $data['rows'][$index]['customer_phone'] = !empty($customer) ? $customer->phone : '';

                    $logistics = LogisticsCompany::model()->findByPk($row['logistics_id']);
                    $data['rows'][$index]['logistics_name'] = !empty($logistics) ? $logistics->name : '';

                    $vehicle = Vehicle::model()->findByPk($row['vehicle_id']);
                    $data['rows'][$index]['vehicle_number'] = !empty($vehicle) ? $vehicle->number : '';
                    $data['rows'][$index]['vehicle_model'] = !empty($vehicle) ? $vehicle->model : '';

                    $oilStation = OilStation::model()->findByPk($row['station_id']);
                    $data['rows'][$index]['oil_station_name'] = !empty($oilStation) ? $oilStation->name : '';
                    $data['rows'][$index]['oil_station_address'] = !empty($oilStation) ? $oilStation->address : '';
                    unset($data['rows'][$index]['create_user_id'], $data['rows'][$index]['update_user_id'], $data['rows'][$index]['update_time'], $data['rows'][$index]['remark']);
                }
            }
            return new \app\cmd\CMDResult(0, $data);
        } catch (Exception $e)
        {
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 获取客户订单列表失败:' . $e->getMessage(), "error", "retail.out");

            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }
    }

    /**
     * @api {POST} / [91010004]获取用户下单使用车辆信息
     * @apiName 91010004
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {int} customer_id 用户id，<font color=red>必填</font>
     * @apiParam (输入字段) {int} station_id 油站id，<font color=red>必填</font>>
     * @apiParam (输入字段) {int} goods_id 油品id，<font color=red>非必填</font>>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91010004"
     * "data":{
     *      "customer_id": "用户id",
     *      "station_id": "油站id",
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "data":[{
     *      "goods_id" => 商品id,
     *      "goods_name" => "品名",
     *      "itmes" => [{
     *          "vehicle_id": "车辆id",
     *          "vehicle_number": "车牌号",
     *          "vehicle_model": "车型",
     *          "max_available_quantity": "车辆最大可加油数量",
     *      }]
     * }]
     * }
     * 失败返回：
     * {
     * "code":1003, //错误码
     * "data":"failed"
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-Order
     * @apiVersion 1.0.0
     */
    protected function getDoOrderVehicles($params)
    {
        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 获取用户下单使用车辆信息入参:' . json_encode($params), "info", "retail.out");

        $requiredParams = ['customer_id', 'station_id'];
        $checkRes = $this->checkArrayParams($params, $requiredParams);

        if ($checkRes !== true)
        {
            return $checkRes;
        }

        try
        {
            if (!empty($params['goods_id']))
            {
                $data = $this->getOrderOutService()->getVehicleMaxOilQuantity($params['customer_id'], $params['station_id'], $params['goods_id']);
            } else
            {
                $data = $this->getOrderOutService()->getVehicleMaxOilQuantity($params['customer_id'], $params['station_id']);
            }

            return new \app\cmd\CMDResult(0, $data);
        } catch (Exception $e)
        {
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 获取用户下单使用车辆信息失败:' . $e->getMessage());

            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage(), "error", "retail.out");
        }
    }
}