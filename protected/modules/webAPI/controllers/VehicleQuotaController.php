<?php
/**
 * Desc: 车辆容量
 * User: wwb
 * Date: 2017/10/9 0009
 * Time: 10:03
 */

use ddd\Quota\Domain\VehicleQuotaLimit\IVehicleQuotaLimitRepository;
class VehicleQuotaController extends WebAPIController
{
    public function pageInit()
    {
        parent::pageInit(); // TODO: Change the autogenerated stub
        $this->rightCode = 'VehicleQuota';
    }
    /**
     * @api {POST} /webAPI/vehicleQuota/list [list]车辆容量
     * @apiName list
     * @apiGroup WebAPI - VehicleQuota
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {string} search 查询字段，<font color=red>非必填</font>
     * @apiParam (输入字段) {int} page 页数 <font color=red>非必填</font>
     * @apiParam (输入字段) {int} pageSize 分页大小 <font color=red>非必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "page":2,
     * "pageSize":15,
     * "search"{
     *      "logistics_name":'朝阳物流',
     *      "number":'粤B21544',
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
     *      "data": [{
     *
     *               "vehicle_id": "1",
     *               "logistics_name": "朝阳物流",
     *               "number": "粤B2102",
     *               "capacity": "12",
     *               "rate":"0.9",
     *               "daily_capacity":"6",
     *               "daily_available_capacity":"6",
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
     * @apiParam (输出字段-data-data) {int} vehicle_id 标识
     * @apiParam (输出字段-data-data) {string} logistics_name 物流企业
     * @apiParam (输出字段-data-data) {string} number 车牌
     * @apiParam (输出字段-data-data) {string} capacity 油箱容量
     * @apiParam (输出字段-data-data) {string} rate 每日额度占比
     * @apiParam (输出字段-data-data) {string} daily_capacity 每日车辆容量
     * @apiParam (输出字段-data-data) {string} daily_available_capacity 每日车辆可用容量
     */
    public function actionList()
    {
        $search = $this->getSearch();
        $attr = array(
            'b.name*'=>$search['logistics_name'],
            'a.number*'=>$search['number'],
        );

        $where = $this->getWhereSql($attr);
        $sql = "select {col} from t_vehicle a
                left join t_logistics_company b on a.logistics_id = b.logistics_id
                left join t_vehicle_daily_quota c on a.vehicle_id=c.vehicle_id and `current_date`=current_date()
                ".$where." order by a.create_time desc,a.vehicle_id desc";

        $fields = "a.vehicle_id, b.name logistics_name, a.number, ifnull(a.capacity, 0) capacity,
                   ifnull(c.used_quota, 0) used_quota, ifnull(c.frozen_quota, 0) frozen_quota";

        $data = $this->getPageData($sql, $fields);
        if(Utility::isEmpty($data->data)){
            $this->returnSuccess([]);
        }

        //每日限额
        $limit = \ddd\Infrastructure\DIService::get(\app\ddd\Quota\Application\VehicleQuotaLimit\VehicleQuotaLimitService::class)->getActiveVehicleQuotaLimit();
        $rate=$limit->rate;

        foreach($data->data as & $datum){
            $dayQuota = $rate * $datum['capacity'];
            $datum['rate'] = $rate;
            $datum['daily_capacity'] = number_format($dayQuota, 2);
            $datum['daily_available_capacity'] = number_format(round($dayQuota, 2) - $datum['used_quota'] - $datum['frozen_quota'],2);
        }

        $this->returnSuccess($data);
    }


    /**
     * @api {POST} /webAPI/vehicleQuota/export [export]导出excel
     * @apiName export
     * @apiGroup WebAPI - VehicleQuota
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {string} search 查询字段，<font color=red>非必填</font>
     * @apiParam (输入字段) {int} page 页数 <font color=red>非必填</font>
     * @apiParam (输入字段) {int} pageSize 分页大小 <font color=red>非必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "page":2,
     * "pageSize":15,
     * "search"{
     *      "logistics_name":'朝阳物流',
     *      "number":'粤B21544',
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
     *      "data": [{
     *
     *               "vehicle_id": "1",
     *               "logistics_name": "朝阳物流",
     *               "number": "粤B2102",
     *               "capacity": "12",
     *               "rate":"0.9",
     *               "daily_capacity":"6",
     *               "daily_available_capacity":"6",
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
     * @apiParam (输出字段-data-data) {int} vehicle_id 标识
     * @apiParam (输出字段-data-data) {string} logistics_name 物流企业
     * @apiParam (输出字段-data-data) {string} number 车牌
     * @apiParam (输出字段-data-data) {string} capacity 油箱容量
     * @apiParam (输出字段-data-data) {string} rate 每日额度占比
     * @apiParam (输出字段-data-data) {string} daily_capacity 每日车辆容量
     * @apiParam (输出字段-data-data) {string} daily_available_capacity 每日车辆可用容量
     */
    public function actionExport()
    {
        $search = $this->getSearch();
        $attr = array(
            'b.name*'=>$search['logistics_name'],
            'a.number*'=>$search['number'],
        );

        $where = $this->getWhereSql($attr);

        $fields = "a.vehicle_id 编号, b.name 物流企业, a.number 车牌号, ifnull(a.capacity, 0) capacity,
                   ifnull(c.used_quota, 0) used_quota, ifnull(c.frozen_quota, 0) frozen_quota";

        $sql = "select ".$fields." from t_vehicle a
                left join t_logistics_company b on a.logistics_id = b.logistics_id
                left join t_vehicle_daily_quota c on a.vehicle_id=c.vehicle_id and `current_date`=current_date()
                ".$where." order by a.create_time desc,a.vehicle_id desc";

        $data = Utility::query($sql);

        //每日限额
        $limit = \ddd\Infrastructure\DIService::get(\app\ddd\Quota\Application\VehicleQuotaLimit\VehicleQuotaLimitService::class)->getActiveVehicleQuotaLimit();
        $rate=$limit->rate;

        foreach($data as & $datum){
            $datum['油箱容量/L'] = $datum['capacity'];
            $datum['每日额度占比'] = round($rate * 100) . '%';
            $dayQuota = $rate * $datum['capacity'];
            $datum['每日车辆容量/L'] = number_format($dayQuota,2);
            $datum['当日车辆可用容量/L'] = number_format(round($dayQuota, 2)-$datum['used_quota']-$datum['frozen_quota'],2);
            unset($datum['capacity']);
            unset($datum['used_quota']);
            unset($datum['frozen_quota']);
        }

        $this->exportExcel($data,'',array(0=>'每日车辆容量/L',1=>'当日车辆可用容量/L',2=>'油箱容量/L',3=>'车牌号'));

    }

    /**
     * @api {POST} /webAPI/vehicleQuota/getDailyQuotaLog [getDailyQuotaLog]车辆每日容量明细列表
     * @apiName getDailyQuotaLog
     * @apiGroup WebAPI - VehicleQuota
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {string} search 查询字段，<font color=red>非必填</font>
     * @apiParam (输入字段) {int} page 页数 <font color=red>非必填</font>
     * @apiParam (输入字段) {int} pageSize 分页大小 <font color=red>非必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "page":2,
     * "pageSize":15,
     * "search"{
     *      "vehicle_id":"1",
     *      "create_time_start":'2018-08-08',
     *      "create_time_end":'2018-08-08',
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
     *      "data": [{
     *               "order_code": "201809140000003",
     *               "quota": "1000",
     *               "create_time": "2018-08-16",
     *          }],
     *      },
     *"extra": {
     *       "logistics_name": "朝阳物流"
     *   }
     *}
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 数据信息
     * @apiParam (输出字段-data) {int} totalPages 列表总页数
     * @apiParam (输出字段-data) {int} totalRows 列表总记录数
     * @apiParam (输出字段-data) {int} page 当前页数
     * @apiParam (输出字段-data) {int} pageSize 每页显示记录数
     * @apiParam (输出字段-data) {array} data 列表行数据信息
     * @apiParam (输出字段-data-data) {int} order_code 订单编号
     * @apiParam (输出字段-data-data) {string} quota 容量明细/元
     * @apiParam (输出字段-data-data) {string} create_time 时间
     * @apiParam (输出字段-data) {array} extra 其他信息
     * @apiParam (输出字段-data-extra) {string} number 车牌号
     */
    public function actionGetDailyQuotaLog()
    {
        $search = $this->getSearch();
        $attr = array(
            'a.create_time>'=>$search['create_time_start'],
            'a.create_time<'=>$search['create_time_end'],
            'a.vehicle_id'=>$search['vehicle_id'],
        );

        $vehicle_id = $search['vehicle_id'];

        try {
            $service = new \app\ddd\Logistics\Application\Vehicle\VehicleService();
            $vehicle = $service->getVehicle($vehicle_id);

            $where = $this->getWhereSql($attr);

            $sql = "SELECT  {col}
                FROM t_vehicle_daily_quota_log a
                     left join t_order b on a.relation_id=b.order_id

                " . $where . " ORDER BY a.log_id DESC";

            $fields = 'a.relation_id,a.create_time,a.quota,b.code order_code';

            $data = $this->getPageData($sql, $fields);

            if (Utility::isEmpty($data->data)) {
                $this->returnSuccess([],['number' => $vehicle['number']]);
            }


            foreach ($data->data as & $datum) {

                $datum['is_can_view'] = false;
                $datum['is_can_edit'] = false;

            }

            $this->returnSuccess($data, ['number' => $vehicle['number']]);

        }
        catch(Exception $e){
            $this->returnError(BusinessError::outputError(\RetailError::$OPERATE_FAILED, array('reason' => $e->getMessage())));
        }

    }



}

