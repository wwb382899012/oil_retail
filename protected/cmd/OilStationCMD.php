<?php

use app\cmd\CMDResult;
use ddd\OilStation\Application\AreaDictTreeService;
use ddd\OilStation\Application\OilStationService;
use ddd\OilStation\Domain\OilCompany\OilCompanyStatusEnum;
use ddd\OilStation\Domain\OilStation\OilStationEnum;

/**
 * User: liyu
 * Date: 2018/9/5
 * Time: 16:18
 * Desc: 油站外部接口
 */
class OilStationCMD extends CMD
{

    public function __construct() {
        $this->actionMap = array(
            "91030001" => "userNearbyOilStationList",
            "91030002" => "oilStationDetail",
            "91030003" => "oilStationList",
            "91030004" => "areaDictTree",
        );
    }

    /**
     * @api {POST} /out [91030004] 获取城市词典树
     * @apiName 91030004
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91030004"
     * "data":{
     *
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     *      "code": 0,
     *      "data": {
     *          "id": 0,
     *          "name": "中国",
     *          "children": [{
     *                  "id": 130000,
     *                  "name": "河北省",
     *                  "children": [{
     *                          "id": 130100,
     *                          "name": "石家庄市",
     *                          "children": []
     *                      },
     *                      {
     *                          "id": 130200,
     *                          "name": "唐山市",
     *                          "children": []
     *                      },
     *                      {
     *                          "id": 130300,
     *                          "name": "秦皇岛市",
     *                          "children": []
     *                      }
     *                  ]
     *              },
     *              {
     *                 "id": 140000,
     *                  "name": "山西省",
     *                  "children": [{
     *                          "id": 140100,
     *                          "name": "太原市",
     *                          "children": []
     *                      },
     *                      {
     *                          "id": 140200,
     *                          "name": "大同市",
     *                          "children": []
     *                      },
     *                      {
     *                          "id": 140300,
     *                          "name": "阳泉市",
     *                          "children": []
     *                      }
     *                  ]
     *              }
     *          ]
     *      }
     * }
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-OilStation
     * @apiVersion 1.0.0
     */
    public function areaDictTree() {
        $data = AreaDictTreeService::service()->getAreaTreeDto();
        return CMDResult::createSuccessResult($data);
    }


    /**
     * @api {POST} /out [91030001]获取用户附近的油站列表
     * @apiName 91030001
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {string} user_id 用户ID，<font color=red>必填</font>
     * @apiParam (输入字段) {string} longitude 当前经度，<font color=red>必填</font>
     * @apiParam (输入字段) {string} latitude 当前纬度，<font color=red>必填</font>
     * @apiParam (输入字段) {string} name 油站名称
     * @apiParam (输入字段) {string} max_distance 最远距离,单位千米 <font color=red>必填</font>
     * @apiParam (输入字段) {string} page 当前页数，<font color=red>必填</font>
     * @apiParam (输入字段) {string} pageSize 每页显示条数，默认20条
     * @apiExample {json} 输入示例:
     *{
     *	"cmd": "91030001",
     *	"data": {
     *		"user_id": 1,
     *		"longitude": 111.667788,
     *		"latitude": 22.667788,
     *		"name": "南山区第一加油站",
     *		"max_distance": 50,
     *		"page": 1,
     *		"pageSize": 20
     *	}
     *}
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     *{
     *    "code": 0,
     *    "data": {
     *        "totalPages": 1,
     *        "totalRows": 10,
     *        "page": 1,
     *        "pageSize": 1,
     *        "data": [
     *            {
     *                "station_id": 1,  //油站ID
     *                "name": "xxx加油站", //油站名称
     *                "company_id": 1,  //油企id
     *                "company_name": "xxxx油品有限公司", //油企名称
     *                "province": "广东省",
     *                "city": "深圳市",
     *                "address": "南山区金牛广场",
     *                "longitude": "113.667788", //经度
     *                "latitude": "23.667788", //纬度
     *                "distance": 11.2, //距离(km)
     *                "closest": 1, //是否最快达到 1/是 2/否
     *                "most_visit": 2  //是否最经常去 1/是 2/否
     *            },
     *            {
     *                "station_id": 2,
     *                "name": "xxx加油站",
     *                "company_id": 1,
     *                "company_name": "xxxx油品有限公司",
     *                "province": "广东省",
     *                "city": "深圳市",
     *                "address": "南山区金牛广场",
     *                "longitude": "113.667788",
     *                "latitude": "23.667788",
     *                "distance": 12.2,
     *                "closest": 2,
     *                "most_visit": 1
     *            },
     *            {
     *                "station_id": 3,
     *                "name": "xxx加油站",
     *                "company_id": 1,
     *                "company_name": "xxxx油品有限公司",
     *                "province": "广东省",
     *                "city": "深圳市",
     *                "address": "南山区金牛广场",
     *                "longitude": "113.667788",
     *                "latitude": "23.667788",
     *                "distance": 13.2,
     *                "closest": 2,
     *                "most_visit": 2
     *            }
     *        ]
     *    }
     *}
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-OilStation
     * @apiVersion 1.0.0
     */
    protected function userNearbyOilStationList($params) {
        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 获取附近的油站列表:' . $params);
        if (empty($params))
            return new \app\cmd\CMDResult(CMDCode::CODE_NO_POST_DATA);
        if (($checkData = $this->checkParams($params)) !== true) {
            Utility::log("获取附近的油站列表接口参数错误，参数为：" . json_encode($params), "error", "retail.out");
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_CHECK_ERROR['code'], $checkData);
        }
        $currentPage = $params['page'] ?? 1;
        $pageSize = $params['pageSize'] ?? Mod::app()->params["pageSize"];
        $data = OilStationService::service()->getUserNearbyOilStationList($params, $currentPage, $pageSize);
        return CMDResult::createSuccessResult($data);
    }

    private function checkParams($params) {
        if (empty($params['longitude']) || empty($params['latitude'])) {
            return '经纬度数值异常';
        }
        if (empty($params['user_id'])) {
            return '获取用户信息错误';
        }
        return true;
    }

    /**
     * @api {POST} /out [91030003]获取所有有效油站列表
     * @apiName 91030003
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {string} name 油站名称
     * @apiParam (输入字段) {string} province_id 省份id
     * @apiParam (输入字段) {string} city_id 城市id
     * @apiParam (输入字段) {string} page 当前页数，<font color=red>必填</font>
     * @apiParam (输入字段) {string} pageSize 每页显示条数，默认20条
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91030003"
     * "data":{
     *      "page":1,
     *      "pageSize":20,
     *      "name":"南山区第一加油站",
     *      "province_id":"4406",
     *      "city_id":"3505",
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     *{
     *    "code": 0,
     *    "data": {
     *        "pageSize": 10,
     *        "totalPages": 1,
     *        "page": 1,
     *        "totalRows": "1",
     *        "data": [
     *            {
     *                "station_id": 1,  //油站ID
     *                "name": "xxx加油站", //油站名称
     *                "company_id": 1,  //油企id
     *                "company_name": "xxxx油品有限公司", //油企名称
     *                "province": "广东省",
     *                "city": "深圳市",
     *                "address": "南山区金牛广场",
     *                "longitude": "113.667788", //经度
     *                "latitude": "23.667788", //纬度
     *                'contact_person'=> '联系人',
     *                'contact_phone'=> '联系电话',
     *                'remark'=> '备注',
     *                "status": "1" //油站状态 0/禁用 1/启用
     *            },
     *            {
     *                "station_id": 2,
     *                "name": "xxx加油站",
     *                "company_id": 1,
     *                "company_name": "xxxx油品有限公司",
     *                "province": "广东省",
     *                "city": "深圳市",
     *                "address": "南山区金牛广场",
     *                "longitude": "113.667788",
     *                "latitude": "23.667788",
     *                'contact_person'=> '联系人',
     *                'contact_phone'=> '联系电话',
     *                'remark'=> '备注',
     *                "status": "1" //油站状态 0/禁用 1/启用
     *            }
     *        ]
     *    }
     *}
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-OilStation
     * @apiVersion 1.0.0
     */
    protected function oilStationList($params) {
        $currentPage = $params['page'] ?? 1;
        $pageSize = $params['pageSize'] ?? Mod::app()->params["pageSize"];

        //物流B端 如果没有状态筛选  只显示正常的油站
        $params['status'] = OilStationEnum::ENABLE;
        $params['company_status'] = OilCompanyStatusEnum::ENABLE;

        $data = OilStationService::service()->getListData($params, $currentPage, $pageSize);

        return CMDResult::createSuccessResult($data);
    }

    /**
     * @api {POST} /out [91030002]获取油站信息
     * @apiName 91030002
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {string} station_id 油站ID，<font color=red>必填</font>
     * @apiParam (输入字段) {string} longitude 经度，<font color=red>必填</font>
     * @apiParam (输入字段) {string} latitude 纬度，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"91030002"
     * "data":{
     *      "station_id":1,
     *      "longitude":113.926044,
     *      "latitude":22.532871,
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     *{
     *    "code": 0,
     *    "data": {
     *        "station_id": 1,
     *        "name": "xxx加油站",
     *        "company_id": 1,
     *        "company_name": "xxxx油品有限公司",
     *        "province": "广东省",
     *        "city": "深圳市",
     *        "address": "南山区金牛广场",
     *        "longitude": "113.667788",
     *        "latitude": "23.667788",
     *        "distance": "11.2",
     *        "status": "1", //油站状态 0/禁用 1/启用
     *        "goods": [
     *            {
     *                "goods_id": 1,  //油品ID
     *                "name": "xxx汽油", //油品名称
     *                "retail_price": 11, //零售价
     *                "agreed_price": 12, //协议价
     *                "discount_price": 13 //优惠价
     *            },
     *            {
     *                "goods_id": 2,
     *                "name": "xxx柴油",
     *                "retail_price": 11,
     *                "agreed_price": 12,
     *                "discount_price": 13
     *            }
     *        ]
     *    }
     *}
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     * @apiGroup Out-OilStation
     * @apiVersion 1.0.0
     */
    protected function oilStationDetail($params) {
        if (empty($params['station_id'])) {
            return new \app\cmd\CMDResult(CMDCode::CODE_PARAM_CHECK_ERROR['code']);
        }
        $params['longitude'] = $params['longitude'] ?? '113.926044';
        $params['latitude'] = $params['latitude'] ?? '22.532871';
        try {
            $dto = OilStationService::service()->getDetailDtoForOut($params);
            return CMDResult::createSuccessResult($dto);
        } catch (Exception $e) {
            return new \app\cmd\CMDResult(CMDCode::CODE_CMD_ERROR, $e->getMessage());
        }

    }

}