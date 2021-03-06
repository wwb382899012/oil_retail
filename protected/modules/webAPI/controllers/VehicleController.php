<?php
/**
 * Desc: 车辆
 * User: wwb
 * Date: 2017/10/9 0009
 * Time: 10:03
 */



class VehicleController extends WebAPIController
{
    public function pageInit()
    {
        parent::pageInit(); // TODO: Change the autogenerated stub
        $this->rightCode = 'vehicleData';
    }

    /**
     * @api {POST} /webAPI/vehicle/list [list]车辆
     * @apiName list
     * @apiGroup WebAPI - vehicle
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {string} search 查询字段，<font color=red>非必填</font>
     * @apiParam (输入字段) {int} page 页数 <font color=red>非必填</font>
     * @apiParam (输入字段) {int} pageSize 分页大小 <font color=red>非必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "page":2,
     * "pageSize":15,
     * "search"{
     *      "logistics_name":'广州凯中石油化工有限公司',
     *      "number":'粤B',
     *      'customer_id':'2',
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     *{
     *"state": 0,
     *"data": {
     *      "pageSize": 10,
     *      "totalPages": 1,
     *      "page": "1",
     *      "totalRows": "1",
     *      "data":[{
     *               "vehicle_id": "1",
     *               "logistics_id":"2",
     *               "number": "粤B23423",
     *               "model": "车型",
     *               "capacity": "120",
     *               "name":"朝阳物流",
     *               "is_can_edit": false,
     *               "is_can_view": false,
     *          }],
     *      }
     *}
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 数据信息
     * @apiParam (输出字段-data) {int} totalPages 列表总页数
     * @apiParam (输出字段-data) {int} totalRows 列表总记录数
     * @apiParam (输出字段-data) {int} page 当前页数
     * @apiParam (输出字段-data) {int} pageSize 每页显示记录数
     * @apiParam (输出字段-data) {array} data 列表行数据信息
     * @apiParam (输出字段-data-data-rows) {int} vehicle_id 车辆id
     * @apiParam (输出字段-data-data-rows) {int} logistics_id 物流企业id
     * @apiParam (输出字段-data-data-rows) {string} number 车牌号
     * @apiParam (输出字段-data-data-rows) {string} model 车辆类型
     * @apiParam (输出字段-data-data-rows) {string} capacity 邮箱容量
     * @apiParam (输出字段-data-data-rows) {string} name 物流企业
     * @apiParam (输出字段-data-data-rows) {string} is_can_edit 是否可修改
     * @apiParam (输出字段-data-data-rows) {string} is_can_view 是否可查看
     */
	public function actionList()
	{
        $search = $this->getSearch();
        $attr = array(
            'd.name*'=>$search['logistics_name'],
            'a.number*'=>$search['number'],
        );

        $where = $this->getWhereSql($attr);
        if(!empty($search['customer_id']))
            $where.=" and a.vehicle_id in(select vehicle_id from t_driver_vehicle_relation e left join t_driver f on e.driver_id=f.driver_id where f.customer_id=".$search['customer_id'].")";
        $sql = "SELECT  {col}
                FROM t_vehicle a
                     left join t_logistics_company d on a.logistics_id=d.logistics_id

                ".$where." ORDER BY a.vehicle_id DESC";

        $fields = 'a.vehicle_id,a.logistics_id,a.number,a.model,a.capacity,d.name logistics_name';

        $data = $this->getPageData($sql, $fields);

        if(Utility::isEmpty($data->data)){
            $this->returnSuccess([]);
        }


        foreach($data->data as & $datum){
            //$datum['out_status_name'] = Map::getStatusName('contract_status', $datum['out_status']);
            //$datum['status_name'] = Map::getStatusName('contract_status', $datum['status']);
            $datum['is_can_view'] = true;
            $datum['is_can_edit'] = false;

        }

        $this->returnSuccess($data);
	}
    /**
     * @api {GET} /webAPI/vehicle/detail [detail]获取车辆信息
     * @apiName detail
     * @apiGroup WebAPI - vehicle
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} vehicle_id 车辆id <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "vehicle_id":2,
     * }
     * @apiSuccessExample {json} 输出示例:
     *{
     *"state": 0,
     *"data": {
     *      "search": null,
     *      "data": {
     *         "vehicle_id": "1",
     *         "number": "粤B23423",
     *         "model": "车型",
     *         "capacity": "120",
     *         "logistics_name":"朝阳物流",
     *         "start_date":"2018-08-01",
     *         "end_date":"2018-08-30",
     *         "status_name":"正常",
     *         "add_time":"2018-08-01 00:00:00,
     *         "operator":"张三",
     *         "files":[
     *                  {'file_id'=>1,"file_url"=>"/data/www/a.jpg"},
     *                  {'file_id'=>2,"file_url"=>"/data/www/b.jpg"},
     *               ],
     *      }
     *}
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 数据信息
     * @apiParam (输出字段-data) {vehicle_id} id 标识id
     * @apiParam (输出字段-data) {string} number 车牌号
     * @apiParam (输出字段-data) {string} logistics_name 物流企业名称
     * @apiParam (输出字段-data) {string} model 车辆类型
     * @apiParam (输出字段-data) {string} capacity 邮箱容量
     * @apiParam (输出字段-data) {string} status_name 审核状态
     * @apiParam (输出字段-data) {string} start_date 行驶证有效期开始时间
     * @apiParam (输出字段-data) {string} end_date 行驶证有效期结束时间
     * @apiParam (输出字段-data) {string} add_time 添加时间
     * @apiParam (输出字段-data) {string} operator 添加人
     * @apiParam (输出字段-data) {string} files 行驶证照片
     */
    public function actionDetail()
    {
        $id = Mod::app()->request->getParam('vehicle_id');
        if (!Utility::checkQueryId($id)) {
            $this->returnError(RetailError::$PARAMS_PASS_ERROR);
        }

        //车辆
        $service = new \app\ddd\Logistics\Application\Vehicle\VehicleService();
        try{

        $vehicle= $service->getVehicle($id);
        if(empty($vehicle))
            $this->returnError(BusinessError::outputError(\RetailError::$VEHICLE_NOT_EXIST,array('vehicle_id'=>$id)));

        $this->returnSuccess($vehicle);

        }catch(Exception $e){
            $this->returnError(BusinessError::outputError(\RetailError::$OPERATE_FAILED, array('reason' => $e->getMessage())));
        }
    }

}

