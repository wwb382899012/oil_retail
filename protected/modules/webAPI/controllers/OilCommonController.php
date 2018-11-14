<?php

use ddd\OilStation\Application\AreaDictTreeService;
use ddd\OilStation\Application\OilCompanyService;

class OilCommonController extends WebAPIController{

    public function pageInit(){
        parent::pageInit();
        $this->authorizedActions = ['dropDownListMap','areaDict'];
    }

    /**
     * @api {POST} /webAPI/oilCommon/dropDownListMap [dropDownListMap] 获取下拉列表map
     * @apiName dropDownListMap
     * @apiGroup WebAPI - OilCommon
     * @apiVersion 1.0.0
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     *{
     *    "code": 0,
     *    "data":[
     *      'ownership' => [], //合作方企业所有制
     *      'oil_company_status' => [], //公司状态
     *      'oil_goods_status' => [], //油品状态
     *      'oil_station_apply_status' => [], //油站申请状态
     *      'oil_station_status' => [], //油站状态
     *      'oil_price_status' => [], //油价状态
     *      'oil_company_id_name_map' => [], //所属公司
     * ]
     *}
     * @apiParam (输出字段) {string} code 错误码，为0时表示成功，其他参考错误码说明
     * @apiParam (输出字段) {string} data 成功或错误信息
     */
    public function actionDropDownListMap(){
        $oil_company_map = OilCompanyService::service()->getAllCompanyIdNames();

        $data = [
            'ownership' => \Map::getKeyValueObject('ownership'),
            'oil_company_status' => \Map::getKeyValueObject('oil_company_status'),
            'oil_goods_status' => \Map::getKeyValueObject('oil_goods_status'),
            'oil_station_apply_status' => \Map::getKeyValueObject('oil_station_apply_status'),
            'oil_station_status' => \Map::getKeyValueObject('oil_station_status'),
            'oil_price_status' => \Map::getKeyValueObject('oil_price_status'),
            'order_status' => \Map::getKeyValueObject('order_status'),
            'oil_company_id_name_map' => $oil_company_map,
        ];

        $this->returnSuccess($data);
    }

    /**
     * @api {POST} /webAPI/oilCommon/areaDict [areaDict] 获取城市词典树
     * @apiName areaDict
     * @apiGroup WebAPI - OilCommon
     * @apiVersion 1.0.0
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     *       "state": 0,
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
     */
    public function actionAreaDict(){
        $data = AreaDictTreeService::service()->getAreaTreeDto();
        $this->returnSuccess($data);
    }
}